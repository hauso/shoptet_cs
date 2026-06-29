<?php

declare(strict_types=1);

namespace App\Provider\PickupPoint;

final class CarrierProviderRegistry
{
    /** @var array<string, PickupPointProviderInterface> */
    private array $providers = [];

    /** @param iterable<PickupPointProviderInterface> $providers */
    public function __construct(iterable $providers)
    {
        foreach ($providers as $provider) {
            $this->providers[$provider->getCarrier()] = $provider;
        }
    }

    public function get(string $carrier): PickupPointProviderInterface
    {
        return $this->providers[$carrier] ?? throw new \InvalidArgumentException(sprintf('Unknown carrier "%s".', $carrier));
    }
}
