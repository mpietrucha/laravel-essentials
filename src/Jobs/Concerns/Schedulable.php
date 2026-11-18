<?php

namespace Mpietrucha\Laravel\Essentials\Jobs\Concerns;

use Illuminate\Console\Scheduling\CallbackEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Schedule;
use Mpietrucha\Support\Concerns\Makeable;

/**
 * @phpstan-require-implements ShouldQueue
 */
trait Schedulable
{
    use Makeable;

    public static function schedule(mixed ...$arguments): CallbackEvent
    {
        $job = static::make(...$arguments);

        return Schedule::job($job);
    }
}
