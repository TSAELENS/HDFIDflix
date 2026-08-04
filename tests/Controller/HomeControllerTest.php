<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use RuntimeException;
use Symfony\Component\HttpClient\Response\MockResponse;

final class HomeControllerTest extends AbstractControllerTestCase 
{
    public function testHomePageDisplaysCatalogSections(): void
    {
        $client = $this->createClientWithTmdb(
            static function (
                string $method,
                string $url,
                array $options,
            ): MockResponse {
                return match (true) {
                    str_contains($url, '/movie/popular') => self::jsonResponse([
                        'results' => [
                            [
                                'id' => 1,
                                'title' => 'Gladiator',
                                'overview' => 'Un général romain devient gladiateur.',
                                'poster_path' => null,
                                'backdrop_path' => null,
                                'release_date' => '2000-05-05',
                            ],
                            [
                                'id' => 2,
                                'title' => 'Pirates des Caraïbes',
                                'overview' => 'Une aventure de pirates.',
                                'poster_path' => null,
                                'backdrop_path' => null,
                                'release_date' => '2003-07-09',
                            ],
                        ],
                    ]),

                    str_contains($url, '/tv/popular') => self::jsonResponse([
                        'results' => [
                            [
                                'id' => 3,
                                'name' => 'Peaky Blinders',
                                'poster_path' => null,
                                'first_air_date' => '2013-09-12',
                            ],
                            [
                                'id' => 4,
                                'name' => 'Breaking Bad',
                                'poster_path' => null,
                                'first_air_date' => '2008-01-20',
                            ],
                        ],
                    ]),

                    str_contains($url, '/movie/top_rated') => self::jsonResponse([
                        'results' => [
                            [
                                'id' => 5,
                                'title' => 'The Dark Knight',
                                'poster_path' => null,
                                'release_date' => '2008-07-18',
                            ],
                        ],
                    ]),

                    default => throw new RuntimeException(
                        'Requête TMDB inattendue : ' . $url,
                    ),
                };
            },
        );

        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('body', 'Gladiator');
        self::assertSelectorTextContains('body', 'Pirates des Caraïbes');
        self::assertSelectorTextContains('body', 'Peaky Blinders');
        self::assertSelectorTextContains('body', 'Breaking Bad');
        self::assertSelectorTextContains('body', 'The Dark Knight');
        self::assertSelectorTextContains('body', 'Films populaires');
        self::assertSelectorTextContains('body', 'Séries populaires');
        self::assertSelectorTextContains('body', 'Films les mieux notés');
    }
}