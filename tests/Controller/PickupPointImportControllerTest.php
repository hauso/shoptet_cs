<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\PickupPoint\PickupPointImportController;
use App\Model\PickupPoint\ImportedPickupPoint;
use App\Model\PickupPoint\PickupPointStatus;
use App\Model\PickupPoint\PickupPointType;
use App\Provider\PickupPoint\PickupPointProviderInterface;
use App\Provider\PickupPoint\CarrierProviderRegistry;
use App\Factory\PickupPoint\PickupPointEntityFactory;
use App\Repository\PickupPoint\PickupPointRepository;
use App\Service\PickupPoint\PickupPointEntityUpdater;
use App\Service\Import\PickupPointImportService;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

final class PickupPointImportControllerTest extends TestCase
{
    public function testImportFlow(): void
    {
        $provider = new class () implements PickupPointProviderInterface {
            public function getCarrier(): string
            {
                return 'balikovna';
            }

            public function provide(): array
            {
                return [new ImportedPickupPoint('1', 'balikovna', PickupPointType::Point, PickupPointStatus::Available, 'Praha', 'Name', 'Addr', '11000', 'CZ', '50.00000000', '14.00000000')];
            }
        };
        $repository = $this->createMock(PickupPointRepository::class);
        $repository->method('findOneByIdentity')->willReturn(null);
        $repository->expects($this->once())->method('save');
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('wrapInTransaction')->willReturnCallback(static fn (callable $callback): mixed => $callback());
        $importService = new PickupPointImportService($entityManager, $repository, new PickupPointEntityFactory(), new PickupPointEntityUpdater());
        $logger = new Logger('test');
        $logger->pushHandler(new NullHandler());
        $controller = new PickupPointImportController(new CarrierProviderRegistry([$provider]), $importService, $logger);

        $result = $controller->import('balikovna');

        self::assertSame(1, $result->processed);
        self::assertSame(1, $result->inserted);
    }
}
