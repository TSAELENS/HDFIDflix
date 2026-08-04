<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SearchControllerTest extends WebTestCase
{
    public function testSearchWithQuery(): void
    {
        $client = static::createClient();

        $client->request('GET', '/search?query=gladiator&type=movie');

        self::assertResponseIsSuccessful();
    }

    public function testSearchWithoutQueryRedirectsToHome(): void
    {
        $client = static::createClient();

        $client->request('GET', '/search');

        self::assertResponseRedirects('/');
    }
}