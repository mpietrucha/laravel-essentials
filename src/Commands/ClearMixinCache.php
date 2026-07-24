<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class ClearMixinCache extends Command
{
    /**
     * @var string
     */
    #[\Override]
    protected $signature = 'essentials:clear-mixin-cache';

    /**
     * @var string
     */
    #[\Override]
    protected $description = 'Clear all generated mixin cache files';

    public function handle(): void
    {
        Process::run(['vendor/bin/phpstan', 'clear-result-cache']);

        $this->callSilently('essentials:generate-mixin-analyzers', ['--flush' => true]);

        $this->info('Mixin cache files cleared successfully.');
    }
}
