<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Mixins\Console\Commands;

use Illuminate\Support\Facades\Process;
use Mpietrucha\Laravel\Essentials\Console\Command;

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

        $this->callSilently(GenerateMixinAnalyzers::class, ['--flush' => true]);

        $this->info('Mixin cache files cleared successfully.');
    }
}
