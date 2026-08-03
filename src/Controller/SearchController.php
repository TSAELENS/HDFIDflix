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
    #[Route('/search', name: 'app_search')]
    public function index(
        Request $request,
        TmdbService $tmdbService,
        EventDispatcherInterface $eventDispatcher
    ): Response {
        $form = $this->createForm(GlobalSearchType::class);

        $query = $request->query->getString('query');
        $selectedType = $request->query->getString('type', 'movie');

        $groupedResults = [
            'movie' => [],
            'tv' => [],
            'person' => [],
            'other' => [],
        ];

        if (!$query) {
            return $this->redirectToRoute('app_home');
        }

        if ($query !== '') {                  
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
            'form' => $form,
            'query' => $query,
            'selectedType' => $selectedType,
            'groupedResults' => $groupedResults,
            'resultCounts' => $resultCounts,
            'results' => $groupedResults[$selectedType],
        ]);
    }
}