<?php

namespace App\Controller;

use App\Form\GlobalSearchType;
use App\Service\TmdbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Event\SearchEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class SearchController extends AbstractController
{
    /**
     * Recherche un terme dans TMDB puis répartit
     * les résultats selon leur type.
     */

    #[Route('/search', name: 'app_search', methods: ['GET'])]
    public function index(
        Request $request,
        TmdbService $tmdbService,
        EventDispatcherInterface $eventDispatcher
    ): Response {
        $form = $this->createForm(GlobalSearchType::class);
        $form->handleRequest($request);

        $query = trim((string) $form->get('query')->getData());
        $selectedType = $request->query->getString('type', 'movie');

        $groupedResults = [
            'movie' => [],
            'tv' => [],
            'person' => [],
            'other' => [],
        ];

        $resultCounts = [
            'movie' => 0,
            'tv' => 0,
            'person' => 0,
            'other' => 0,
        ];

        if (!$form->isSubmitted()) {
            return $this->redirectToRoute('app_home');
        }

        if ($form->isValid()) {
            $eventDispatcher->dispatch(
                new SearchEvent($query)
            );
            $response = $tmdbService->search($query);
            $results = $response['results'] ?? [];

            foreach ($results as $result) {
                $mediaType = $result['media_type'] ?? 'other';

                if (isset($groupedResults[$mediaType])) {
                    $groupedResults[$mediaType][] = $result;
                } else {
                    $groupedResults['other'][] = $result;
                }
            }
            $resultCounts = [
                'movie' => count($groupedResults['movie']),
                'tv' => count($groupedResults['tv']),
                'person' => count($groupedResults['person']),
                'other' => count($groupedResults['other']),
            ];
        }

        if (!array_key_exists($selectedType, $groupedResults)) {
            $selectedType = 'movie';
        }

        return $this->render('search/index.html.twig', [
            'form' => $form->createView(),
            'query' => $query,
            'selectedType' => $selectedType,
            'groupedResults' => $groupedResults,
            'resultCounts' => $resultCounts,
            'results' => $groupedResults[$selectedType],
        ]);
    }
}