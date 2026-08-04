<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractControllerTestCase extends WebTestCase
{
    protected function createClientWithTmdb(callable $responseFactory): KernelBrowser
    {
        $client = static::createClient();

        static::getContainer()->set(
            HttpClientInterface::class,
            new MockHttpClient($responseFactory),
        );

        return $client;
    }

    protected static function jsonResponse(
        array $data,
        int $statusCode = 200,
    ): MockResponse {
        return new MockResponse(
            json_encode($data, JSON_THROW_ON_ERROR),
            [
                'http_code' => $statusCode,
                'response_headers' => [
                    'content-type: application/json',
                ],
            ],
        );
    }
}