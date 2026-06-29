<?php

declare(strict_types=1);

namespace App\Model\PickupPoint;

final readonly class ImportedPickupPoint
{
    public function __construct(
        public string $externalId,
        public string $carrier,
        public PickupPointType $type,
        public PickupPointStatus $status,
        public string $city,
        public string $name,
        public string $address,
        public string $zipCode,
        public string $country,
        public string $latitude,
        public string $longitude,
        public ?string $openingHours = null,
    ) {
        if ($externalId === '' || $carrier === '' || $city === '' || $name === '' || $address === '' || $zipCode === '') {
            throw new \InvalidArgumentException('Pickup point contains an empty required text field.');
        }
        if (!preg_match('/^[A-Z]{2}$/', $country)) {
            throw new \InvalidArgumentException('Country must be an ISO 3166-1 alpha-2 code.');
        }
    }
}
