<?php

declare(strict_types=1);

namespace App\Service\PickupPoint;

use App\Entity\PickupPoint\PickupPoint;
use App\Model\PickupPoint\ImportedPickupPoint;

final class PickupPointEntityUpdater
{
    public function update(PickupPoint $entity, ImportedPickupPoint $data): bool
    {
        if (!$this->hasChanged($entity, $data)) {
            return false;
        }

        $entity->update(
            type: $data->type,
            status: $data->status,
            city: $data->city,
            name: $data->name,
            address: $data->address,
            zipCode: $data->zipCode,
            latitude: $data->latitude,
            longitude: $data->longitude,
            openingHours: $data->openingHours,
        );

        return true;
    }

    private function hasChanged(PickupPoint $entity, ImportedPickupPoint $data): bool
    {
        return $entity->getType() !== $data->type
            || $entity->getStatus() !== $data->status
            || $entity->getCity() !== $data->city
            || $entity->getName() !== $data->name
            || $entity->getAddress() !== $data->address
            || $entity->getZipCode() !== $data->zipCode
            || $entity->getOpeningHours() !== $data->openingHours
            || $this->coordinate($entity->getLatitude()) !== $this->coordinate($data->latitude)
            || $this->coordinate($entity->getLongitude()) !== $this->coordinate($data->longitude);
    }

    private function coordinate(string $value): string
    {
        return number_format((float) $value, 8, '.', '');
    }
}
