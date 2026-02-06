<?php

namespace Mpietrucha\Laravel\Essentials\Commands;

use Illuminate\Console\Command;

class GenerateMixinAnalyzers extends Command
{
    /**
     * @var string
     */
    protected $signature = 'mixin:analyzers {directory : Namespace of analyzer class}';

    /**
     * @var string
     */
    protected $description = 'Generate PHPStan analyzer files for registered mixins';

    public function handle(): void
    {
    }
}
