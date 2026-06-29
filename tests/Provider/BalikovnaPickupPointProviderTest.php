<?php

declare(strict_types=1);

namespace App\Tests\Provider;

use App\Mapper\Balikovna\BalikovnaPickupPointMapper;
use App\Provider\Balikovna\BalikovnaPickupPointProvider;
use App\Tests\Support\FixtureHttpClient;
use PHPUnit\Framework\TestCase;

final class BalikovnaPickupPointProviderTest extends TestCase
{
    public function testProvidesPointsFromNamespacedFixtureHttpClient(): void
    {
        $provider = new BalikovnaPickupPointProvider(new FixtureHttpClient((string) file_get_contents(__DIR__ . '/../Fixture/balikovna.xml')), new BalikovnaPickupPointMapper(), 'fixture');
        $points = $provider->provide();

        self::assertCount(2, $points);
        self::assertStringStartsWith('balikovna-', $points[0]->externalId);
        self::assertSame('Praha 10', $points[0]->name);
    }

    public function testEmptyResponseFails(): void
    {
        $provider = new BalikovnaPickupPointProvider(new FixtureHttpClient(''), new BalikovnaPickupPointMapper(), 'fixture');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('empty');
        $provider->provide();
    }

    public function testInvalidXmlFailsWithReadableError(): void
    {
        $provider = new BalikovnaPickupPointProvider(new FixtureHttpClient('<rowset><row></rowset>'), new BalikovnaPickupPointMapper(), 'fixture');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('not valid XML');
        $provider->provide();
    }

    public function testResponseWithoutRowsFailsWithReadableError(): void
    {
        $provider = new BalikovnaPickupPointProvider(new FixtureHttpClient('<zv xmlns="urn:test"><generated>2026-06-30</generated></zv>'), new BalikovnaPickupPointMapper(), 'fixture');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('does not contain any pickup point rows');
        $provider->provide();
    }
}
