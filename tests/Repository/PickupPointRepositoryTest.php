<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\PickupPoint\PickupPoint;
use App\Model\PickupPoint\PickupPointStatus;
use App\Model\PickupPoint\PickupPointType;
use App\Repository\PickupPoint\PickupPointRepository;
use PHPUnit\Framework\TestCase;

final class PickupPointRepositoryTest extends TestCase
{
    public function testSaveDelegatesToEntityManager(): void
    {
        $repository = $this->getMockBuilder(PickupPointRepository::class)->disableOriginalConstructor()->onlyMethods(['getEntityManager'])->getMock();
        $entity = new PickupPoint('BAL123', 'balikovna', PickupPointType::Point, PickupPointStatus::Available, 'Praha', 'Balíkovna', 'Ulice 1', '11000', 'CZ', '50.00000000', '14.00000000');
        $entityManager = $this->createMock(\Doctrine\ORM\EntityManagerInterface::class);
        $entityManager->expects($this->once())->method('persist')->with($entity);
        $repository->method('getEntityManager')->willReturn($entityManager);

        $repository->save($entity);
    }
}
