<?php

declare(strict_types=1);

namespace App\Model\Import;

final readonly class ImportResult
{
    public function __construct(
        public string $carrier,
        public int $processed,
        public int $inserted,
        public int $updated,
    ) {
    }
}
