<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

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
    /*                          RECHERCHE GLOBALE                             */
    /* ====================================================================== */
    public function search(string $query, int $page = 1): array
    {
        $response = $this->httpClient->request(
            'GET',
            self::BASE_URL . '/search/multi',
            [
                'auth_bearer' => $this->apiToken,
                'query' => [
                    'query' => $query,
                    'language' => 'fr-FR',
                    'include_adult' => false,
                    'page' => $page,
                ],
            ],
        );

        return $response->toArray();
    }

    /* ====================================================================== */
    /*                     DÉTAILS DES CONTENUS TMDB                          */
    /* ====================================================================== */

    public function getMovieDetails(int $id): array
    {
        $response = $this->httpClient->request(
            'GET',
            self::BASE_URL . '/movie/' . $id,
            [
                'auth_bearer' => $this->apiToken,
                'query' => [
                    'language' => 'fr-FR',
                    'append_to_response' => 'videos,credits,watch/providers,similar',
                ],
            ],
        );

        return $response->toArray();
    }

    public function getTvDetails(int $id): array
    {
        $response = $this->httpClient->request(
            'GET',
            self::BASE_URL . '/tv/' . $id,
            [
                'auth_bearer' => $this->apiToken,
                'query' => [
                    'language' => 'fr-FR',
                    'append_to_response' => 'videos,aggregate_credits,watch/providers,similar',
                ],
            ],
        );

        return $response->toArray();
    }

        public function getPersonDetails(int $id): array
    {
        $response = $this->httpClient->request(
            'GET',
            self::BASE_URL . '/person/' . $id,
            [
                'auth_bearer' => $this->apiToken,
                'query' => [
                    'language' => 'fr-FR',
                    'append_to_response' => 'combined_credits',
                ],
            ],
        );

        return $response->toArray();
    }

    /* ====================================================================== */
    /*                    CONTENUS DE LA PAGE D’ACCUEIL                       */
    /* ====================================================================== */

    public function getPopularMovies(): array
    {
        $response = $this->httpClient->request(
            'GET',
            self::BASE_URL . '/movie/popular',
            [
                'auth_bearer' => $this->apiToken,
                'query' => [
                    'language' => 'fr-FR',
                ],
            ],
        );

        return $response->toArray()['results'];
    }


    public function getPopularTv(): array
    {
        $response = $this->httpClient->request(
            'GET',
            self::BASE_URL . '/tv/popular',
            [
                'auth_bearer' => $this->apiToken,
                'query' => [
                    'language' => 'fr-FR',
                ],
            ],
        );

        return $response->toArray()['results'];
    }


    public function getTopRatedMovies(): array
    {
        $response = $this->httpClient->request(
            'GET',
            self::BASE_URL . '/movie/top_rated',
            [
                'auth_bearer' => $this->apiToken,
                'query' => [
                    'language' => 'fr-FR',
                ],
            ],
        );

        return $response->toArray()['results'];
    }

}