<?php

namespace Mpietrucha\Laravel\Essentials\Mixins\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Mpietrucha\Laravel\Essentials\Macro\Mixin;
use Mpietrucha\Laravel\Essentials\Macro\MixinAnalyzer;
use Mpietrucha\Support\ClassNamespace;
use Mpietrucha\Support\Filesystem;
use Mpietrucha\Support\Filesystem\Path;
use Mpietrucha\Support\Filesystem\Touch;

/**
 * @phpstan-import-type MixinTarget from Mixin
 * @phpstan-import-type MixinHandlerCollection from Mixin
 */
class GenerateMixinAnalyzers extends Command
{
    /**
     * @var string
     */
    #[\Override]
    protected $signature = 'essentials:generate-mixin-analyzers
                            {--directory=phpstan/cache : The output directory for generated analyzer files}
                            {--cwd= : The current working directory used for generating analyzers}
                            {--flush : Clear all cached analyzer files before regenerating }';

    /**
     * @var string
     */
    #[\Override]
    protected $description = 'Generate PHPStan analyzer files for registered mixins';

    public function handle(): void
    {
        if ($this->option('flush')) {
            $this->directory() |> Filesystem::cleanDirectory(...);

            $this->info('Mixin analyzers flushed successfully.');

            return;
        }

        $analyzers = Mixin::storage()->map(function (Collection $handlers, string $target): ?string {
            $content = MixinAnalyzer::render($target, $handlers);

            if ($content === null) {
                return null;
            }

            $file = $this->file($target);

            Filesystem::put($file, $content);

            return $file;
        })->filter();

        if ($analyzers->isEmpty()) {
            $this->warn('No registered mixins found. Register mixins in your service provider before running this command.');

            return;
        }

        $analyzers->each(fn (string $analyzer) => $this->components->task($analyzer));

        sprintf('%s mixin analyzer(s) generated in [%s].', $analyzers->count(), $this->directory()) |> $this->info(...);
    }

    protected function file(string $target): string
    {
        $directory = $this->directory();

        return Path::build(ClassNamespace::toFile($target), $directory) |> Touch::file(...);
    }

    protected function directory(): string
    {
        /** @var string $directory */
        $directory = $this->option('directory');

        /** @var string $cwd */
        $cwd = $this->option('cwd') ?? Path::directory(__DIR__, 3);

        return Path::build($directory, $cwd);
    }
}
