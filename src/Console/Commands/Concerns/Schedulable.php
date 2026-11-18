<?php

namespace Mpietrucha\Laravel\Essentials\Console\Commands\Concerns;

use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Support\Facades\Schedule;
use Mpietrucha\Support\Concerns\Makeable;

/**
 * @phpstan-require-extends Command
 */
trait Schedulable
{
    use Makeable;

    public static function schedule(mixed ...$arguments): Event
    {
        $command = static::make(...$arguments);

        return Schedule::command($command);
    }
}
