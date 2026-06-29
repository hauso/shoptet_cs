<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Factory\PickupPoint\PickupPointEntityFactory;
use App\Model\PickupPoint\ImportedPickupPoint;
use App\Model\PickupPoint\PickupPointStatus;
use App\Model\PickupPoint\PickupPointType;
use PHPUnit\Framework\TestCase;

final class PickupPointEntityFactoryTest extends TestCase
{
    public function testCreatesEntityFromImportData(): void
    {
        $data = new ImportedPickupPoint('BAL123', 'balikovna', PickupPointType::Box, PickupPointStatus::Available, 'Praha', 'Box', 'Ulice 1', '11000', 'CZ', '50.00000000', '14.00000000', '<hours/>');
        $entity = (new PickupPointEntityFactory())->create($data);

        self::assertSame('BAL123', $entity->getExternalId());
        self::assertSame('balikovna', $entity->getCarrier());
        self::assertSame(PickupPointType::Box, $entity->getType());
        self::assertSame('<hours/>', $entity->getOpeningHours());
    }
}
