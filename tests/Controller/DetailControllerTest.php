<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Response;

final class DetailControllerTest extends AbstractControllerTestCase 
{
    public function testMovieDetailsPage(): void
    {
        $client = $this->createClientWithTmdb(
            static fn (
                string $method,
                string $url,
                array $options,
            ): MockResponse => self::jsonResponse([
                'id' => 1,
                'title' => 'Gladiator',
                'tagline' => 'Ce que nous faisons dans la vie résonne dans l’éternité.',
                'overview' => 'Un général romain trahi devient gladiateur.',
                'poster_path' => null,
                'backdrop_path' => null,
                'release_date' => '2000-05-05',
                'vote_average' => 8.2,
                'runtime' => 155,
                'genres' => [
                    ['name' => 'Action'],
                    ['name' => 'Drame'],
                ],
                'credits' => [
                    'cast' => [
                        [
                            'id' => 6,
                            'name' => 'Russell Crowe',
                            'character' => 'Maximus',
                            'profile_path' => null,
                        ],
                        [
                            'id' => 8,
                            'name' => 'Joaquin Phoenix',
                            'character' => 'Commode',
                            'profile_path' => null,
                        ],
                    ],
                ],
                'videos' => [
                    'results' => [],
                ],
                'watch/providers' => [
                    'results' => [],
                ],
                'similar' => [
                    'results' => [],
                ],
            ]),
        );

        $client->request('GET', '/movie/1');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Gladiator');
        self::assertSelectorTextContains('body', 'Synopsis');
        self::assertSelectorTextContains('body', 'Russell Crowe');
        self::assertSelectorTextContains('body', 'Joaquin Phoenix');
    }

    public function testTvDetailsPage(): void
    {
        $client = $this->createClientWithTmdb(
            static fn (
                string $method,
                string $url,
                array $options,
            ): MockResponse => self::jsonResponse([
                'id' => 4,
                'name' => 'Breaking Bad',
                'tagline' => null,
                'overview' => 'Un professeur de chimie se lance dans le trafic de drogue.',
                'poster_path' => null,
                'backdrop_path' => null,
                'first_air_date' => '2008-01-20',
                'vote_average' => 9.0,
                'number_of_seasons' => 5,
                'genres' => [
                    ['name' => 'Drame'],
                    ['name' => 'Crime'],
                ],
                'aggregate_credits' => [
                    'cast' => [
                        [
                            'id' => 9,
                            'name' => 'Bryan Cranston',
                            'profile_path' => null,
                            'roles' => [
                                ['character' => 'Walter White'],
                            ],
                        ],
                    ],
                ],
                'videos' => [
                    'results' => [],
                ],
                'watch/providers' => [
                    'results' => [],
                ],
                'similar' => [
                    'results' => [],
                ],
            ]),
        );

        $client->request('GET', '/tv/4');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Breaking Bad');
        self::assertSelectorTextContains('body', '5 saisons');
        self::assertSelectorTextContains('body', 'Bryan Cranston');
    }

    public function testPersonDetailsPage(): void
    {
        $client = $this->createClientWithTmdb(
            static fn (
                string $method,
                string $url,
                array $options,
            ): MockResponse => self::jsonResponse([
                'id' => 7,
                'name' => 'Ryan Gosling',
                'profile_path' => null,
                'birthday' => '1980-11-12',
                'deathday' => null,
                'place_of_birth' => 'London, Ontario, Canada',
                'known_for_department' => 'Acting',
                'biography' => 'Biographie de test de Ryan Gosling.',
                'combined_credits' => [
                    'cast' => [
                        [
                            'id' => 10,
                            'media_type' => 'movie',
                            'title' => 'Blade Runner 2049',
                            'poster_path' => null,
                            'release_date' => '2017-10-04',
                        ],
                    ],
                ],
            ]),
        );

        $client->request('GET', '/person/7');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Ryan Gosling');
        self::assertSelectorTextContains('body', 'Biographie');
        self::assertSelectorTextContains('body', 'Blade Runner 2049');
    }

    public function testMovieRouteRejectsInvalidId(): void
    {
        $client = static::createClient();

        $client->request('GET', '/movie/invalide');

        self::assertResponseStatusCodeSame(
            Response::HTTP_NOT_FOUND,
        );
    }
}