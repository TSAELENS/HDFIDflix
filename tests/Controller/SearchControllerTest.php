<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use RuntimeException;
use Symfony\Component\HttpClient\Response\MockResponse;

final class SearchControllerTest extends AbstractControllerTestCase 
{
    public function testSearchWithoutQueryRedirectsToHome(): void
    {
        $client = static::createClient();

        $client->request('GET', '/search');

        self::assertResponseRedirects('/');
    }

    public function testMovieSearchDisplaysOnlyMovieResults(): void
    {
        $client = $this->createClientWithTmdb(
            static function (
                string $method,
                string $url,
                array $options,
            ): MockResponse {
                if (!str_contains($url, '/search/multi')) {
                    throw new RuntimeException(
                        'Requête TMDB inattendue : ' . $url,
                    );
                }

                return self::jsonResponse([
                    'page' => 1,
                    'total_pages' => 1,
                    'total_results' => 4,
                    'results' => [
                        [
                            'id' => 1,
                            'media_type' => 'movie',
                            'title' => 'Gladiator',
                            'poster_path' => null,
                            'release_date' => '2000-05-05',
                        ],
                        [
                            'id' => 3,
                            'media_type' => 'tv',
                            'name' => 'Peaky Blinders',
                            'poster_path' => null,
                            'first_air_date' => '2013-09-12',
                        ],
                        [
                            'id' => 6,
                            'media_type' => 'person',
                            'name' => 'Russell Crowe',
                            'profile_path' => null,
                        ],
                        [
                            'id' => 7,
                            'media_type' => 'person',
                            'name' => 'Ryan Gosling',
                            'profile_path' => null,
                        ],
                    ],
                ]);
            },
        );

        $client->request(
            'GET',
            '/search?query=gladiator&type=movie',
        );

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('body', 'gladiator');

        self::assertSelectorTextContains(
            '.search-results',
            'Gladiator',
        );

        self::assertSelectorTextNotContains(
            '.search-results',
            'Peaky Blinders',
        );

        self::assertSelectorTextNotContains(
            '.search-results',
            'Russell Crowe',
        );

        self::assertSelectorTextNotContains(
            '.search-results',
            'Ryan Gosling',
        );
    }
}