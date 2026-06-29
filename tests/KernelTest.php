<?php

declare(strict_types=1);

namespace App\Tests;

use App\Command\ImportPickupPointsCommand;
use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use PHPUnit\Framework\TestCase;

final class KernelTest extends TestCase
{
    /** @var callable|null */
    private $previousExceptionHandler = null;

    protected function setUp(): void
    {
        $this->previousExceptionHandler = set_exception_handler(static function (): void {
        });
        restore_exception_handler();
    }

    protected function tearDown(): void
    {
        $this->restoreExceptionHandlerStack();
    }

    public function testContainerBuildsConsoleCommandFromServicesYaml(): void
    {
        $this->configureEnvironment();

        $kernel = new Kernel('test', true);
        $kernel->boot();

        $container = $kernel->getContainer();

        self::assertTrue($container->has(ImportPickupPointsCommand::class));
        self::assertInstanceOf(ImportPickupPointsCommand::class, $container->get(ImportPickupPointsCommand::class));

        $kernel->shutdown();
    }

    public function testConsoleApplicationContainsImportCommand(): void
    {
        $this->configureEnvironment();

        $kernel = new Kernel('test', true);
        $kernel->boot();
        $application = new Application($kernel);

        self::assertTrue($application->has('app:pickup-points:import'));
        self::assertTrue($application->has('doctrine:migrations:migrate'));

        $kernel->shutdown();
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
