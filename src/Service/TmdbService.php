<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Exception\TmdbException;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final class TmdbService
{
    private const BASE_URL = 'https://api.themoviedb.org/3';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        #[Autowire('%env(TMDB_API_TOKEN)%')]
        private readonly string $apiToken,
    ) {
    }

    /* ====================================================================== */
    /*                          RECHERCHE GLOBALE                              */
    /* ====================================================================== */

    public function search(string $query, int $page = 1): array
    {
        return $this->request('/search/multi', [
            'query' => $query,
            'include_adult' => false,
            'page' => $page,
        ]);
    }

    /* ====================================================================== */
    /*                     DÉTAILS DES CONTENUS TMDB                           */
    /* ====================================================================== */

    public function getMovieDetails(int $id): array
    {
        return $this->request('/movie/' . $id, [
            'append_to_response' => 'videos,credits,watch/providers,similar',
        ]);
    }

    public function getTvDetails(int $id): array
    {
        return $this->request('/tv/' . $id, [
            'append_to_response' => 'videos,aggregate_credits,watch/providers,similar',
        ]);
    }

    public function getPersonDetails(int $id): array
    {
        return $this->request('/person/' . $id, [
            'append_to_response' => 'combined_credits',
        ]);
    }

    /* ====================================================================== */
    /*                    CONTENUS DE LA PAGE D’ACCUEIL                        */
    /* ====================================================================== */

    public function getPopularMovies(): array
    {
        return $this->request('/movie/popular')['results'] ?? [];
    }

    public function getPopularTv(): array
    {
        return $this->request('/tv/popular')['results'] ?? [];
    }

    public function getTopRatedMovies(): array
    {
        return $this->request('/movie/top_rated')['results'] ?? [];
    }

    /* ====================================================================== */
    /*                          REQUÊTE COMMUNE                                */
    /* ====================================================================== */

    /**
     * Effectue une requête GET vers l’API TMDB.
     */
    private function request(string $endpoint, array $query = []): array
    {
        try {
            $response = $this->httpClient->request(
                'GET',
                self::BASE_URL . $endpoint,
                [
                    'auth_bearer' => $this->apiToken,
                    'query' => array_merge(
                        [
                            'language' => 'fr-FR',
                        ],
                        $query,
                    ),
                ],
            );

            $statusCode = $response->getStatusCode();

            if ($statusCode === 401) {
                throw new TmdbException('Le token TMDB est invalide.');
            }

            if ($statusCode === 404) {
                throw new TmdbException('La ressource TMDB demandée est introuvable.');
            }

            if ($statusCode === 429) {
                throw new TmdbException('La limite de requêtes TMDB a été atteinte.');
            }

            if ($statusCode >= 500) {
                throw new TmdbException('Le service TMDB est temporairement indisponible.');
            }

            if ($statusCode >= 400) {
                throw new TmdbException(
                    sprintf('Erreur TMDB : code HTTP %d.', $statusCode),
                );
            }

            return $response->toArray(false);
        } catch (TransportExceptionInterface $exception) {
            throw new TmdbException(
                'Impossible de contacter le service TMDB.',
                previous: $exception,
            );
        } catch (DecodingExceptionInterface $exception) {
            throw new TmdbException(
                'La réponse reçue de TMDB est invalide.',
                previous: $exception,
            );
        }
    }
}