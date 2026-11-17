<?php

namespace Mpietrucha\Laravel\Essentials\Jobs\Concerns;

use Illuminate\Console\Scheduling\CallbackEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Schedule;

/**
 * @phpstan-require-implements ShouldQueue
 */
trait Schedulable
{
    public static function schedule(mixed ...$arguments): CallbackEvent
    {
        /** @var static $job */
        $job = resolve(static::class, $arguments);

        return Schedule::job($job);
    }
}
