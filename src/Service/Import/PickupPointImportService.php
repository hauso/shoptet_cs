<?php

declare(strict_types=1);

namespace App\Service\Import;

use App\Factory\PickupPoint\PickupPointEntityFactory;
use App\Model\Import\ImportResult;
use App\Model\PickupPoint\ImportedPickupPoint;
use App\Repository\PickupPoint\PickupPointRepository;
use App\Service\PickupPoint\PickupPointEntityUpdater;
use Doctrine\ORM\EntityManagerInterface;

final readonly class PickupPointImportService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PickupPointRepository $repository,
        private PickupPointEntityFactory $factory,
        private PickupPointEntityUpdater $updater,
    ) {
    }

    /** @param list<ImportedPickupPoint> $points */
    public function import(string $carrier, array $points): ImportResult
    {
        return $this->entityManager->wrapInTransaction(function () use ($carrier, $points): ImportResult {
            $inserted = 0;
            $updated = 0;

            foreach ($points as $point) {
                if ($point->carrier !== $carrier) {
                    throw new \InvalidArgumentException(sprintf('Pickup point "%s" belongs to carrier "%s", expected "%s".', $point->externalId, $point->carrier, $carrier));
                }

                $entity = $this->repository->findOneByIdentity($carrier, $point->externalId, $point->country);
                if ($entity === null) {
                    $this->repository->save($this->factory->create($point));
                    ++$inserted;
                    continue;
                }

                if ($this->updater->update($entity, $point)) {
                    ++$updated;
                }
            }

            return new ImportResult($carrier, count($points), $inserted, $updated);
        });
    }
}
