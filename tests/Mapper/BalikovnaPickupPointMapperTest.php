<?php

declare(strict_types=1);

namespace App\Tests\Mapper;

use App\Mapper\Balikovna\BalikovnaPickupPointMapper;
use App\Model\PickupPoint\PickupPointStatus;
use App\Model\PickupPoint\PickupPointType;
use PHPUnit\Framework\TestCase;

final class BalikovnaPickupPointMapperTest extends TestCase
{
    public function testMapsBalikovnaRow(): void
    {
        $xml = new \SimpleXMLElement('<row><PSC>10000</PSC><OBEC>Praha</OBEC><C_OBEC>Strašnice</C_OBEC><NAZEV>Praha 10</NAZEV><ADRESA>Černokostelecká 2020/20</ADRESA><TYP>pošta</TYP><OTEV_DOBY><den name="Po"><od>08:00</od><do>18:00</do></den></OTEV_DOBY><SOUR_X_WGS84>14.492777</SOUR_X_WGS84><SOUR_Y_WGS84>50.076442</SOUR_Y_WGS84></row>');
        $point = (new BalikovnaPickupPointMapper())->map($xml);

        self::assertStringStartsWith('balikovna-', $point->externalId);
        self::assertStringStartsNotWith('10000', $point->externalId);
        self::assertSame('balikovna', $point->carrier);
        self::assertSame(PickupPointType::Point, $point->type);
        self::assertSame(PickupPointStatus::Available, $point->status);
        self::assertSame('Praha', $point->city);
        self::assertSame('50.07644200', $point->latitude);
        self::assertSame('14.49277700', $point->longitude);
        self::assertNotNull($point->openingHours);
        self::assertStringContainsString('<OTEV_DOBY>', $point->openingHours);
    }

    public function testMapsNamespacedBalikovnaRowAndCityFallback(): void
    {
        $xml = new \SimpleXMLElement('<row xmlns="urn:test"><PSC>60200</PSC><C_OBEC>Brno-město</C_OBEC><NAZEV>Balíkovna Brno box</NAZEV><ADRESA>Nádražní 1</ADRESA><TYP>BalikoBOX</TYP><SOUR_X_WGS84>16.612</SOUR_X_WGS84><SOUR_Y_WGS84>49.191</SOUR_Y_WGS84></row>');
        $point = (new BalikovnaPickupPointMapper())->map($xml);

        self::assertSame('Brno-město', $point->city);
        self::assertSame('49.19100000', $point->latitude);
        self::assertSame('16.61200000', $point->longitude);
        self::assertSame(PickupPointType::Box, $point->type);
    }

    public function testInvalidLongitudeFails(): void
    {
        $xml = new \SimpleXMLElement('<row><PSC>11000</PSC><OBEC>Praha</OBEC><NAZEV>Balíkovna Praha 1</NAZEV><ADRESA>Jindřišská 14</ADRESA><SOUR_X_WGS84>999</SOUR_X_WGS84><SOUR_Y_WGS84>50.08465</SOUR_Y_WGS84></row>');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('longitude');
        (new BalikovnaPickupPointMapper())->map($xml);
    }

    public function testInvalidLatitudeFails(): void
    {
        $xml = new \SimpleXMLElement('<row><PSC>11000</PSC><OBEC>Praha</OBEC><NAZEV>Balíkovna Praha 1</NAZEV><ADRESA>Jindřišská 14</ADRESA><SOUR_X_WGS84>14.42995</SOUR_X_WGS84><SOUR_Y_WGS84>999</SOUR_Y_WGS84></row>');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('latitude');
        (new BalikovnaPickupPointMapper())->map($xml);
    }
}
