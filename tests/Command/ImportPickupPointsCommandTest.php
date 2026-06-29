<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\ImportPickupPointsCommand;
use App\Controller\PickupPoint\PickupPointImportController;
use App\Model\Import\ImportResult;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class ImportPickupPointsCommandTest extends TestCase
{
    public function testUnknownCarrierReturnsFailure(): void
    {
        $controller = $this->createMock(PickupPointImportController::class);
        $controller->method('import')->willThrowException(new \InvalidArgumentException('Unknown carrier "unknown".'));
        $tester = new CommandTester(new ImportPickupPointsCommand($controller));
        self::assertSame(Command::FAILURE, $tester->execute(['carrier' => 'unknown']));
    }

    public function testSuccessfulImportPrintsResult(): void
    {
        $controller = $this->createMock(PickupPointImportController::class);
        $controller->method('import')->with('balikovna')->willReturn(new ImportResult('balikovna', 2, 1, 0));
        $tester = new CommandTester(new ImportPickupPointsCommand($controller));
        self::assertSame(Command::SUCCESS, $tester->execute(['carrier' => 'balikovna']));
        self::assertStringContainsString('Processed: 2', $tester->getDisplay());
    }
}
