<?php

declare(strict_types=1);

namespace App\Command;

use App\Controller\PickupPoint\PickupPointImportController;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:pickup-points:import')]
final class ImportPickupPointsCommand extends Command
{
    public function __construct(private readonly PickupPointImportController $controller)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Imports pickup points for a carrier.')
            ->addArgument('carrier', InputArgument::REQUIRED, 'Carrier code, e.g. balikovna');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $carrier = (string) $input->getArgument('carrier');

        if ($carrier === '') {
            $output->writeln('<error>Carrier is required.</error>');
            return Command::INVALID;
        }

        try {
            $result = $this->controller->import($carrier);
        } catch (\Throwable $exception) {
            $output->writeln('<error>Pickup point import failed: ' . $exception->getMessage() . '</error>');
            return Command::FAILURE;
        }

        $output->writeln(sprintf('Carrier: %s', $result->carrier));
        $output->writeln(sprintf('Processed: %d', $result->processed));
        $output->writeln(sprintf('Inserted: %d', $result->inserted));
        $output->writeln(sprintf('Updated: %d', $result->updated));

        return Command::SUCCESS;
    }
}
