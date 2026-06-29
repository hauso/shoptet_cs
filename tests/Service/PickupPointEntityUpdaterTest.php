<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\PickupPoint\PickupPoint;
use App\Model\PickupPoint\ImportedPickupPoint;
use App\Model\PickupPoint\PickupPointStatus;
use App\Model\PickupPoint\PickupPointType;
use App\Service\PickupPoint\PickupPointEntityUpdater;
use PHPUnit\Framework\TestCase;

final class PickupPointEntityUpdaterTest extends TestCase
{
    public function testReturnsFalseWhenNothingChanged(): void
    {
        $entity = $this->entity();
        $data = $this->data();

        self::assertFalse((new PickupPointEntityUpdater())->update($entity, $data));
    }

    public function testReturnsTrueAndUpdatesChangedFields(): void
    {
        $entity = $this->entity();
        $data = $this->data(address: 'Ulice 2', status: PickupPointStatus::TemporarilyUnavailable, openingHours: '<changed/>');

        self::assertTrue((new PickupPointEntityUpdater())->update($entity, $data));
        self::assertSame('Ulice 2', $entity->getAddress());
        self::assertSame(PickupPointStatus::TemporarilyUnavailable, $entity->getStatus());
        self::assertSame('<changed/>', $entity->getOpeningHours());
    }

    public function testCoordinatesAreComparedOnEightDecimalPlaces(): void
    {
        $entity = $this->entity(latitude: '50.123456781', longitude: '14.123456781');
        $data = $this->data(latitude: '50.123456780', longitude: '14.123456780');

        self::assertFalse((new PickupPointEntityUpdater())->update($entity, $data));
    }

    private function entity(string $latitude = '50.00000000', string $longitude = '14.00000000'): PickupPoint
    {
        return new PickupPoint('BAL123', 'balikovna', PickupPointType::Point, PickupPointStatus::Available, 'Praha', 'Balíkovna', 'Ulice 1', '11000', 'CZ', $latitude, $longitude, '<hours/>');
    }

    private function data(string $address = 'Ulice 1', PickupPointStatus $status = PickupPointStatus::Available, ?string $openingHours = '<hours/>', string $latitude = '50.00000000', string $longitude = '14.00000000'): ImportedPickupPoint
    {
        return new ImportedPickupPoint('BAL123', 'balikovna', PickupPointType::Point, $status, 'Praha', 'Balíkovna', $address, '11000', 'CZ', $latitude, $longitude, $openingHours);
    }
}
