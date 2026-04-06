<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Commands\Concerns;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Process;

/**
 * @phpstan-require-extends Command
 */
trait InteractsWithLint
{
    /**
     * @param  string|list<string>  $files
     */
    protected function lint(iterable|string $files): void
    {
        $files = Collection::wrap($files);

        Process::run(['composer', 'lint', ...$files]);
        Process::run(['npm', 'run', 'lint', ...$files]);
    }
}
