<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Console;

use Illuminate\Console\Command as IlluminateCommand;
use Mpietrucha\Laravel\Essentials\Console\Commands\Concerns\Schedulable;

abstract class Command extends IlluminateCommand
{
    use Schedulable;
}
