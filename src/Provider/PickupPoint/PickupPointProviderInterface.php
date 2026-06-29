<?php

declare(strict_types=1);

namespace App\Provider\PickupPoint;

use App\Model\PickupPoint\ImportedPickupPoint;

interface PickupPointProviderInterface
{
    public function getCarrier(): string;

    /** @return list<ImportedPickupPoint> */
    public function provide(): array;
}
