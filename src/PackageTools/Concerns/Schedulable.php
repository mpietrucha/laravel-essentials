<?php

namespace Mpietrucha\Laravel\Essentials\PackageTools\Concerns;

use Illuminate\Console\Scheduling\Schedule;
use Mpietrucha\Laravel\Essentials\Console\Scheduling\PendingSchedule;
use Mpietrucha\Laravel\Essentials\PackageTools\PackageServiceProvider;

/**
 * @phpstan-require-extends PackageServiceProvider
 */
trait Schedulable
{
    public function schedule(): PendingSchedule
    {
        $pendingSchedule = PendingSchedule::make();

        $this->callAfterResolving(
            Schedule::class,
            static fn (Schedule $schedule) => $pendingSchedule->apply($schedule),
        );

        return $pendingSchedule;
    }
}
