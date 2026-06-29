<?php

declare(strict_types=1);

namespace App\Repository\PickupPoint;

use App\Entity\PickupPoint\PickupPoint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<PickupPoint> */
class PickupPointRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PickupPoint::class);
    }

    public function findOneByIdentity(string $carrier, string $externalId, string $country): ?PickupPoint
    {
        return $this->findOneBy(['carrier' => $carrier, 'externalId' => $externalId, 'country' => $country]);
    }

    public function save(PickupPoint $pickupPoint): void
    {
        $this->getEntityManager()->persist($pickupPoint);
    }
}
