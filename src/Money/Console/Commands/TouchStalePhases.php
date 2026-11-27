<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Money\Console\Commands;

use Mpietrucha\Laravel\Essentials\Console\Command;
use Mpietrucha\Laravel\Essentials\Money\Jobs\TouchStalePhases as TouchStalePhasesJob;

class TouchStalePhases extends Command
{
    /**
     * @var string
     */
    #[\Override]
    protected $signature = 'essentials:touch-stale-phases';

    /**
     * @var string
     */
    #[\Override]
    protected $description = 'Touch phases whose status has changed since last updated_at';

    public function handle(): void
    {
        TouchStalePhasesJob::make() |> dispatch_sync(...);

        $this->info('Stale phases touched successfully.');
    }
}
