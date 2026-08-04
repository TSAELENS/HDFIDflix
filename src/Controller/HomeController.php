<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\GlobalSearchType;
use Symfony\Component\HttpFoundation\Request;
use App\Service\TmdbService;

final class HomeController extends AbstractController
{
    /**
     * Affiche la page d’accueil avec les contenus populaires
     * et traite le formulaire de recherche global.
     */

    #[Route('/', name: 'app_home')]
    public function index(Request $request, TmdbService $tmdbService): Response
    {
        $form = $this->createForm(GlobalSearchType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('app_search', [
                'query' => $form->get('query')->getData(),
            ]);
        }

        return $this->render('home/index.html.twig', [
            'form' => $form,
            'popularMovies' => $tmdbService->getPopularMovies(),
            'popularTv' => $tmdbService->getPopularTv(),
            'topRatedMovies' => $tmdbService->getTopRatedMovies(),
        ]);
    }
}
