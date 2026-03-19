<?php

namespace Mpietrucha\Laravel\Essentials\Commands;

use Illuminate\Console\Command;
use Mpietrucha\Laravel\Essentials\Commands\Concerns\InteractsWithLint;

class GenerateIdeHelpers extends Command
{
    use InteractsWithLint;

    /**
     * @var string
     */
    #[\Override]
    protected $signature = 'essentials:ide-helpers';

    /**
     * @var string
     */
    #[\Override]
    protected $description = 'Generate IDE helpers';

    public function handle(): void
    {
        $this->callSilently('ide-helper:generate');
        $this->callSilently('ide-helper:models', ['--write-mixin' => true]);

        app_path('Models') |> $this->lint(...);

        $this->info('Helper files generated successfully.');
    }
}
