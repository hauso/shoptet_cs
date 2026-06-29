<?php

declare(strict_types=1);

namespace App\Service;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

class HttpClient
{
    public function __construct(private ClientInterface $client, private float $timeout)
    {
    }

    public function get(string $url): string
    {
        try {
            $response = $this->client->request('GET', $url, ['timeout' => $this->timeout, 'http_errors' => false]);
        } catch (GuzzleException $exception) {
            throw new \RuntimeException(sprintf('HTTP request failed for %s: %s', $url, $exception->getMessage()), 0, $exception);
        }

        $statusCode = $response->getStatusCode();
        if ($statusCode < 200 || $statusCode >= 300) {
            throw new \RuntimeException(sprintf('HTTP request failed for %s with status %d.', $url, $statusCode));
        }

        $body = trim((string) $response->getBody());
        if ($body === '') {
            throw new \RuntimeException(sprintf('HTTP request for %s returned an empty response.', $url));
        }

        return $body;
    }
}
