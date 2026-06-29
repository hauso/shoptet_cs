<?php

declare(strict_types=1);

namespace App\Mapper\Balikovna;

use App\Model\PickupPoint\ImportedPickupPoint;
use App\Model\PickupPoint\PickupPointStatus;
use App\Model\PickupPoint\PickupPointType;
use SimpleXMLElement;

final class BalikovnaPickupPointMapper
{
    public function map(SimpleXMLElement $item): ImportedPickupPoint
    {
        $zipCode = $this->read($item, ['PSC', 'psc', 'zipCode']);
        $city = $this->read($item, ['OBEC', 'C_OBEC', 'obec', 'city']);
        $name = $this->read($item, ['NAZEV', 'nazev', 'name']);
        $address = $this->read($item, ['ADRESA', 'adresa', 'address']);
        $type = $this->readOptional($item, ['TYP', 'typ', 'type']) ?? '';
        $lat = $this->read($item, ['SOUR_Y_WGS84', 'latitude', 'LATITUDE', 'lat']);
        $lon = $this->read($item, ['SOUR_X_WGS84', 'longitude', 'LONGITUDE', 'lon', 'lng']);
        $openingHours = $this->readOptionalXml($item, ['OTEV_DOBY', 'oteviraci_doba', 'openingHours']);
        $externalId = $this->readOptional($item, ['ID', 'id', 'KOD', 'kod', 'ID_PROVOZOVNY', 'id_provozovny'])
            ?? $this->fallbackExternalId($name, $address, $zipCode, $city);

        return new ImportedPickupPoint(
            externalId: $externalId,
            carrier: 'balikovna',
            type: $this->type($name, $type),
            status: PickupPointStatus::Available,
            city: $city,
            name: $name,
            address: $address,
            zipCode: $zipCode,
            country: 'CZ',
            latitude: $this->decimal($lat, -90.0, 90.0, 'latitude'),
            longitude: $this->decimal($lon, -180.0, 180.0, 'longitude'),
            openingHours: $openingHours,
        );
    }

    /** @param list<string> $names */
    private function read(SimpleXMLElement $item, array $names): string
    {
        $value = $this->readOptional($item, $names);
        if ($value === null) {
            throw new \InvalidArgumentException(sprintf('Missing Balikovna feed field: %s.', implode('/', $names)));
        }

        return $value;
    }

    /** @param list<string> $names */
    private function readOptional(SimpleXMLElement $item, array $names): ?string
    {
        foreach ($names as $name) {
            $nodes = $item->xpath('./*[local-name() = ' . $this->xpathLiteral($name) . ']') ?: [];
            foreach ($nodes as $node) {
                $value = trim((string) $node);
                if ($value !== '') {
                    return $value;
                }
            }
        }

        return null;
    }

    /** @param list<string> $names */
    private function readOptionalXml(SimpleXMLElement $item, array $names): ?string
    {
        foreach ($names as $name) {
            $nodes = $item->xpath('./*[local-name() = ' . $this->xpathLiteral($name) . ']') ?: [];
            foreach ($nodes as $node) {
                $xml = $node->asXML();
                if ($xml !== false && trim($xml) !== '') {
                    return trim($xml);
                }
            }
        }

        return null;
    }

    private function decimal(string $value, float $min, float $max, string $field): string
    {
        $normalized = str_replace(',', '.', trim($value));
        if (!is_numeric($normalized)) {
            throw new \InvalidArgumentException(sprintf('Balikovna %s value "%s" is not a valid decimal number.', $field, $value));
        }

        $number = (float) $normalized;
        if ($number < $min || $number > $max) {
            throw new \InvalidArgumentException(sprintf('Balikovna %s value "%s" is outside allowed range.', $field, $value));
        }

        return number_format($number, 8, '.', '');
    }

    private function type(string $name, string $type): PickupPointType
    {
        $value = mb_strtolower($name . ' ' . $type);

        return str_contains($value, 'box') || str_contains($value, 'balikobox') || str_contains($value, 'balíkobox')
            ? PickupPointType::Box
            : PickupPointType::Point;
    }

    private function fallbackExternalId(string $name, string $address, string $zipCode, string $city): string
    {
        return 'balikovna-' . hash('sha256', implode('|', ['balikovna', $name, $address, $zipCode, $city]));
    }

    private function xpathLiteral(string $value): string
    {
        return sprintf('"%s"', str_replace('"', '&quot;', $value));
    }
}
