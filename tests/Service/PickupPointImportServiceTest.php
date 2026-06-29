<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\PickupPoint\PickupPoint;
use App\Factory\PickupPoint\PickupPointEntityFactory;
use App\Model\PickupPoint\ImportedPickupPoint;
use App\Model\PickupPoint\PickupPointStatus;
use App\Model\PickupPoint\PickupPointType;
use App\Repository\PickupPoint\PickupPointRepository;
use App\Service\PickupPoint\PickupPointEntityUpdater;
use App\Service\Import\PickupPointImportService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class PickupPointImportServiceTest extends TestCase
{
    public function testRepeatedImportIsIdempotent(): void
    {
        $repository = $this->createMock(PickupPointRepository::class);
        $entityManager = $this->transactionalEntityManager();
        $service = new PickupPointImportService($entityManager, $repository, new PickupPointEntityFactory(), new PickupPointEntityUpdater());
        $point = $this->point();
        $entity = new PickupPoint('BAL123', 'balikovna', PickupPointType::Point, PickupPointStatus::Available, 'Praha', 'Balíkovna', 'Ulice 1', '11000', 'CZ', '50.00000000', '14.00000000');

        $repository->expects($this->exactly(2))->method('findOneByIdentity')->willReturnOnConsecutiveCalls(null, $entity);
        $repository->expects($this->once())->method('save')->with($this->isInstanceOf(PickupPoint::class));

        $first = $service->import('balikovna', [$point]);
        $second = $service->import('balikovna', [$point]);

        self::assertSame(1, $first->inserted);
        self::assertSame(0, $first->updated);
        self::assertSame(0, $second->inserted);
        self::assertSame(0, $second->updated);
    }

    public function testRejectsCarrierMismatch(): void
    {
        $service = new PickupPointImportService($this->transactionalEntityManager(), $this->createMock(PickupPointRepository::class), new PickupPointEntityFactory(), new PickupPointEntityUpdater());

        $this->expectException(\InvalidArgumentException::class);
        $service->import('balikovna', [$this->point(carrier: 'other')]);
    }

    private function point(string $carrier = 'balikovna'): ImportedPickupPoint
    {
        return new ImportedPickupPoint('BAL123', $carrier, PickupPointType::Point, PickupPointStatus::Available, 'Praha', 'Balíkovna', 'Ulice 1', '11000', 'CZ', '50.00000000', '14.00000000');
    }

    private function transactionalEntityManager(): EntityManagerInterface
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('wrapInTransaction')->willReturnCallback(static fn (callable $callback): mixed => $callback());

        return $entityManager;
    }
}
