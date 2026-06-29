<?php

declare(strict_types=1);

namespace App\Controller\PickupPoint;

use App\Model\Import\ImportResult;
use App\Service\Import\PickupPointImportService;
use App\Provider\PickupPoint\CarrierProviderRegistry;
use Psr\Log\LoggerInterface;

class PickupPointImportController
{
    public function __construct(
        private readonly CarrierProviderRegistry $registry,
        private readonly PickupPointImportService $importService,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function import(string $carrier): ImportResult
    {
        $this->logger->info('Pickup point import started.', ['carrier' => $carrier]);
        try {
            $provider = $this->registry->get($carrier);
            $points = $provider->provide();
            $result = $this->importService->import($carrier, $points);
            $this->logger->info('Pickup point import finished.', ['carrier' => $carrier, 'processed' => $result->processed, 'inserted' => $result->inserted, 'updated' => $result->updated]);

            return $result;
        } catch (\Throwable $exception) {
            $this->logger->error('Pickup point import failed.', ['carrier' => $carrier, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }
}
