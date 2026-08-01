<?php

namespace App\Controller;

use App\Service\TmdbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DetailController extends AbstractController
{
    #[Route('/movie/{id}', name: 'app_movie_details', requirements: ['id' => '\d+'])]
    public function movie(int $id, TmdbService $tmdbService): Response
    {
        $media = $tmdbService->getMovieDetails($id);

        return $this->render('detail/index.html.twig', [
            'media' => $media,
            'type' => 'movie',
        ]);
    }

    #[Route('/tv/{id}', name: 'app_tv_details', requirements: ['id' => '\d+'])]
    public function tv(int $id, TmdbService $tmdbService): Response
    {
        $media = $tmdbService->getTvDetails($id);

        return $this->render('detail/index.html.twig', [
            'media' => $media,
            'type' => 'tv',
        ]);
    }
}