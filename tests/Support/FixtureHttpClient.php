<?php

declare(strict_types=1);

namespace App\Tests\Support;

use App\Service\HttpClient;
use GuzzleHttp\Client;

final class FixtureHttpClient extends HttpClient
{
    public function __construct(private readonly string $body)
    {
        parent::__construct(new Client(), 1.0);
    }

    public function get(string $url): string
    {
        return $this->body;
    }
}
