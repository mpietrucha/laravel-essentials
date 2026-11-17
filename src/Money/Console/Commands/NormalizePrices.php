<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Money\Console\Commands;

use Mpietrucha\Laravel\Essentials\Console\Command;
use Mpietrucha\Laravel\Essentials\Money\Jobs\NormalizePrices as NormalizePricesJob;

/**
 * @phpstan-import-type ModelDirectories from NormalizePricesJob
 */
class NormalizePrices extends Command
{
    /**
     * @var string
     */
    #[\Override]
    protected $signature = 'essentials:normalize-prices
                            {directories?* : Directories to scan for models}';

    /**
     * @var string
     */
    #[\Override]
    protected $description = 'Normalize prices for all models with HasPrice traits';

    public function handle(): void
    {
        /** @var ModelDirectories $modelDirectories */
        $modelDirectories = $this->argument('directories') ?: null;

        NormalizePricesJob::make($modelDirectories) |> dispatch_sync(...);

        $this->info('Prices normalized successfully.');
    }
}
