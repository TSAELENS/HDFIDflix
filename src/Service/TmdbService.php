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
}