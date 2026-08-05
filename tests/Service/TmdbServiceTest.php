<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Exception\TmdbException;
use App\Service\TmdbService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class TmdbServiceTest extends TestCase
{
    private const API_TOKEN = 'test-token';

    public function testSearchSendsCorrectRequest(): void
    {
        $httpClient = new MockHttpClient(
            function (string $method, string $url, array $options, ): MockResponse {
                self::assertSame('GET', $method);
                self::assertStringStartsWith(
                    'https://api.themoviedb.org/3/search/multi?',
                    $url,
                );

                self::assertContains(
                    'Authorization: Bearer ' . self::API_TOKEN,
                    $options['headers'],
                );

                return $this->jsonResponse([
                    'results' => [],
                ]);
            },
        );

        $service = new TmdbService(
            $httpClient,
            self::API_TOKEN,
        );

        $result = $service->search('gladiator', 2);

        self::assertSame(
            [
                'results' => [],
            ],
            $result,
        );
    }

    public function testMovieDetailsSendsCorrectRequest(): void
    {
        $httpClient = new MockHttpClient(
            function (string $method, string $url, array $options, ): MockResponse {
                self::assertSame('GET', $method);
                self::assertStringStartsWith(
                    'https://api.themoviedb.org/3/movie/1?',
                    $url,
                );

                self::assertContains(
                    'Authorization: Bearer ' . self::API_TOKEN,
                    $options['headers'],
                );

                return $this->jsonResponse([
                    'id' => 1,
                    'title' => 'Gladiator',
                ]);
            },
        );

        $service = new TmdbService(
            $httpClient,
            self::API_TOKEN,
        );

        $result = $service->getMovieDetails(1);

        self::assertSame('Gladiator', $result['title']);
    }

    public function testUnauthorizedResponseThrowsException(): void
    {
        $service = $this->createServiceWithResponse(
            new MockResponse('', [
                'http_code' => 401,
            ]),
        );

        $this->expectException(TmdbException::class);
        $this->expectExceptionMessageIs(
            'Le token TMDB est invalide.',
        );

        $service->getPopularMovies();
    }

    public function testNotFoundResponseThrowsException(): void
    {
        $service = $this->createServiceWithResponse(
            new MockResponse('', [
                'http_code' => 404,
            ]),
        );

        $this->expectException(TmdbException::class);
        $this->expectExceptionMessageIs(
            'La ressource TMDB demandée est introuvable.',
        );

        $service->getMovieDetails(999999);
    }

    public function testTooManyRequestsResponseThrowsException(): void
    {
        $service = $this->createServiceWithResponse(
            new MockResponse('', [
                'http_code' => 429,
            ]),
        );

        $this->expectException(TmdbException::class);
        $this->expectExceptionMessageIs(
            'La limite de requêtes TMDB a été atteinte.',
        );

        $service->getPopularMovies();
    }

    public function testServerErrorResponseThrowsException(): void
    {
        $service = $this->createServiceWithResponse(
            new MockResponse('', [
                'http_code' => 500,
            ]),
        );

        $this->expectException(TmdbException::class);
        $this->expectExceptionMessageIs(
            'Le service TMDB est temporairement indisponible.',
        );

        $service->getPopularMovies();
    }

    public function testInvalidJsonThrowsException(): void
    {
        $service = $this->createServiceWithResponse(
            new MockResponse('JSON invalide', [
                'http_code' => 200,
                'response_headers' => [
                    'content-type: application/json',
                ],
            ]),
        );

        $this->expectException(TmdbException::class);
        $this->expectExceptionMessageIs(
            'La réponse reçue de TMDB est invalide.',
        );

        $service->getPopularMovies();
    }

    public function testNetworkErrorThrowsException(): void
    {
        $httpClient = new MockHttpClient(
            static function (): never {
                throw new TransportException(
                    'Erreur réseau simulée.',
                );
            },
        );

        $service = new TmdbService(
            $httpClient,
            self::API_TOKEN,
        );

        $this->expectException(TmdbException::class);
        $this->expectExceptionMessageIs(
            'Impossible de contacter le service TMDB.',
        );

        $service->getPopularMovies();
    }

    private function createServiceWithResponse(
        MockResponse $response,
    ): TmdbService {
        return new TmdbService(
            new MockHttpClient($response),
            self::API_TOKEN,
        );
    }

    private function jsonResponse(array $data): MockResponse
    {
        return new MockResponse(
            json_encode(
                $data,
                JSON_THROW_ON_ERROR,
            ),
            [
                'http_code' => 200,
                'response_headers' => [
                    'content-type: application/json',
                ],
            ],
        );
    }
}