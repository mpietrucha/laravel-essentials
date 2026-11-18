<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Mpietrucha\Laravel\Essentials\Jobs\Concerns\Schedulable;
use Mpietrucha\Support\Concerns\Makeable;

abstract class Job implements ShouldQueue
{
    use Makeable;
    use Queueable;
    use Schedulable;
}
