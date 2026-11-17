<?php

namespace Mpietrucha\Laravel\Essentials\Console\Commands\Concerns;

use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Support\Facades\Schedule;

/**
 * @phpstan-require-extends Command
 */
trait Schedulable
{
    public static function schedule(): Event
    {
        return Schedule::command(static::class);
    }
}
