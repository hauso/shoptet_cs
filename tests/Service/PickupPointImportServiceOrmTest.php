<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\PickupPoint\PickupPoint;
use App\Factory\PickupPoint\PickupPointEntityFactory;
use App\Kernel;
use App\Model\PickupPoint\ImportedPickupPoint;
use App\Model\PickupPoint\PickupPointStatus;
use App\Model\PickupPoint\PickupPointType;
use App\Repository\PickupPoint\PickupPointRepository;
use App\Service\Import\PickupPointImportService;
use App\Service\PickupPoint\PickupPointEntityUpdater;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

final class PickupPointImportServiceOrmTest extends TestCase
{
    private \Symfony\Component\HttpKernel\KernelInterface $kernel;
    private EntityManagerInterface $entityManager;
    private PickupPointImportService $service;
    private PickupPointRepository $repository;
    /** @var callable|null */
    private $previousExceptionHandler = null;

    protected function setUp(): void
    {
        $this->previousExceptionHandler = set_exception_handler(static function (): void {
        });
        restore_exception_handler();

        $this->configureEnvironment();
        $this->kernel = new Kernel('test', true);
        $this->kernel->boot();
        $container = $this->kernel->getContainer();

        /** @var ManagerRegistry $doctrine */
        $doctrine = $container->get('doctrine');
        $entityManager = $doctrine->getManager();
        self::assertInstanceOf(EntityManagerInterface::class, $entityManager);
        $this->entityManager = $entityManager;
        $metadata = [$this->entityManager->getClassMetadata(PickupPoint::class)];
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);

        /** @var PickupPointRepository $repository */
        $repository = $this->entityManager->getRepository(PickupPoint::class);
        $this->repository = $repository;
        $this->service = new PickupPointImportService(
            $this->entityManager,
            $this->repository,
            new PickupPointEntityFactory(),
            new PickupPointEntityUpdater(),
        );
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->kernel->shutdown();
        $this->restoreExceptionHandlerStack();
    }

    private function restoreExceptionHandlerStack(): void
    {
        while (true) {
            $currentHandler = set_exception_handler(static function (): void {
            });
            restore_exception_handler();

            if ($currentHandler === $this->previousExceptionHandler) {
                return;
            }

            restore_exception_handler();
        }
    }

    public function testImportIsIdempotentAndUpdatesChangedData(): void
    {
        $first = $this->service->import('balikovna', [$this->point()]);
        $this->entityManager->clear();
        $second = $this->service->import('balikovna', [$this->point()]);
        $this->entityManager->clear();
        $changed = $this->service->import('balikovna', [$this->point(address: 'Changed 2', status: PickupPointStatus::TemporarilyUnavailable, openingHours: 'Mo-Fr 8-16')]);
        $this->entityManager->clear();

        self::assertSame(1, $first->inserted);
        self::assertSame(0, $first->updated);
        self::assertSame(0, $second->inserted);
        self::assertSame(0, $second->updated);
        self::assertSame(0, $changed->inserted);
        self::assertSame(1, $changed->updated);
        self::assertCount(1, $this->repository->findBy(['carrier' => 'balikovna', 'externalId' => 'BAL123', 'country' => 'CZ']));
    }

    private function point(
        string $address = 'Ulice 1',
        PickupPointStatus $status = PickupPointStatus::Available,
        ?string $openingHours = null,
    ): ImportedPickupPoint {
        return new ImportedPickupPoint('BAL123', 'balikovna', PickupPointType::Point, $status, 'Praha', 'Balíkovna', $address, '11000', 'CZ', '50.000000001', '14.000000001', $openingHours);
    }

    private function configureEnvironment(): void
    {
        $_ENV['BALIKOVNA_FEED_URL'] = 'https://example.test/balikovna.xml';
        $_SERVER['BALIKOVNA_FEED_URL'] = 'https://example.test/balikovna.xml';
        $_ENV['HTTP_TIMEOUT'] = '1.0';
        $_SERVER['HTTP_TIMEOUT'] = '1.0';
        $_ENV['DATABASE_URL'] = 'sqlite:///:memory:';
        $_SERVER['DATABASE_URL'] = 'sqlite:///:memory:';
        $_ENV['APP_SECRET'] = 'test-secret';
        $_SERVER['APP_SECRET'] = 'test-secret';
        $_ENV['APP_ENV'] = 'test';
        $_SERVER['APP_ENV'] = 'test';
    }
}
