<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Console\Commands;

use Barryvdh\LaravelIdeHelper\Console\GeneratorCommand;
use Barryvdh\LaravelIdeHelper\Console\ModelsCommand;
use Mpietrucha\Laravel\Essentials\Console\Command;
use Mpietrucha\Laravel\Essentials\Console\Commands\Concerns\InteractsWithLint;

class GenerateIdeHelpers extends Command
{
    use InteractsWithLint;

    /**
     * @var string
     */
    #[\Override]
    protected $signature = 'essentials:generate-ide-helpers';

    /**
     * @var string
     */
    #[\Override]
    protected $description = 'Generate IDE helpers';

    public function handle(): void
    {
        $this->callSilently(GeneratorCommand::class);
        $this->callSilently(ModelsCommand::class, ['--write-mixin' => true]);

        app_path('Models') |> $this->lint(...);

        $this->info('Helper files generated successfully.');
    }
}
