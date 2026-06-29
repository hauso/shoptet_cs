<?php

declare(strict_types=1);

namespace App\Factory\PickupPoint;

use App\Entity\PickupPoint\PickupPoint;
use App\Model\PickupPoint\ImportedPickupPoint;

final class PickupPointEntityFactory
{
    public function create(ImportedPickupPoint $data): PickupPoint
    {
        return new PickupPoint(
            externalId: $data->externalId,
            carrier: $data->carrier,
            type: $data->type,
            status: $data->status,
            city: $data->city,
            name: $data->name,
            address: $data->address,
            zipCode: $data->zipCode,
            country: $data->country,
            latitude: $data->latitude,
            longitude: $data->longitude,
            openingHours: $data->openingHours,
        );
    }
}
