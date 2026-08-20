<?php

namespace Mpietrucha\Laravel\Essentials\Money\Commands;

use Illuminate\Console\Command;
use Mpietrucha\Laravel\Essentials\Money\Jobs\NormalizePrices as NormalizePricesJob;

class NormalizePrices extends Command
{
    /**
     * @var string
     */
    #[\Override]
    protected $signature = 'essentials:normalize-prices
                            {--directory=* : Directories to scan for models}';

    /**
     * @var string
     */
    #[\Override]
    protected $description = 'Normalize prices for all models with HasPrice traits';

    public function handle(): void
    {
        $this->option('directory') |> NormalizePricesJob::dispatchSync(...);
    }
}
