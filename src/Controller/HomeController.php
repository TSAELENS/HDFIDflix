<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\GlobalSearchType;
use App\Service\TmdbService;


final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, TmdbService $tmdbService): Response
    {
        $form = $this->createForm(GlobalSearchType::class);
        $form->handleRequest($request);

        $results = [];
        $query = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $query = $data['query'];

            $response = $tmdbService->search($query);
            $results = $response['results'] ?? [];
        }

        return $this->render('home/index.html.twig', [
            'form' => $form,
            'results' => $results,
            'query' => $query,
        ]);
    }
}
