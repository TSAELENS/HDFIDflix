<?php

namespace App\Controller;

use App\Service\TmdbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Event\ViewedEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class DetailController extends AbstractController
{
    /* ====================================================================== */
    /*                              FICHE FILM                                */
    /* ====================================================================== */

    #[Route('/movie/{id}', name: 'app_movie_details', requirements: ['id' => '\d+'])]
    public function movie(int $id, TmdbService $tmdbService, EventDispatcherInterface $eventDispatcher): Response
    {
        $media = $tmdbService->getMovieDetails($id);

        $eventDispatcher->dispatch(
            new ViewedEvent(
                $id,
                $media['title'] ?? 'Sans titre',
                'movie',
            )
        );

        return $this->render('detail/index.html.twig', [
            'media' => $media,
            'type' => 'movie',
        ]);
    }

    /* ====================================================================== */
    /*                              FICHE SÉRIE                               */
    /* ====================================================================== */

    #[Route('/tv/{id}', name: 'app_tv_details', requirements: ['id' => '\d+'])]
    public function tv(int $id, TmdbService $tmdbService, EventDispatcherInterface $eventDispatcher): Response
    {
        $media = $tmdbService->getTvDetails($id);

        $eventDispatcher->dispatch(new ViewedEvent($id, $media['name']?? 'Sans titre', 'tv'));

        return $this->render('detail/index.html.twig', [
            'media' => $media,
            'type' => 'tv',
        ]);
    }

    /* ====================================================================== */
    /*                            FICHE PERSONNE                              */
    /* ====================================================================== */

    #[Route('/person/{id}', name: 'app_person_details', requirements: ['id' => '\d+'])]
    public function person(int $id,TmdbService $tmdbService, EventDispatcherInterface $eventDispatcher): Response
    {
        $person = $tmdbService->getPersonDetails($id);
        $eventDispatcher->dispatch(new ViewedEvent($id,  $person['name'] ?? 'Sans nom', 'person'));

        return $this->render('detail/person.html.twig', [
            'person' => $person,
        ]);
    }

}