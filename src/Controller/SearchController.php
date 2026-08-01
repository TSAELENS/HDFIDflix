<?php

namespace App\Controller;

use App\Form\GlobalSearchType;
use App\Service\TmdbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function index(
        Request $request,
        TmdbService $tmdbService
    ): Response {
        $form = $this->createForm(GlobalSearchType::class);

        $query = $request->query->get('query');
        $results = [];

        if ($query) {
            $response = $tmdbService->search($query);
            $results = $response['results'] ?? [];
        }

        return $this->render('search/index.html.twig', [
            'form' => $form,
            'query' => $query,
            'results' => $results,
        ]);
    }
}