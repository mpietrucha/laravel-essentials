<?php

namespace Mpietrucha\Laravel\Essentials\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Mpietrucha\Laravel\Essentials\Commands\Concerns\InteractsWithLint;
use Mpietrucha\Laravel\Essentials\Macro\Mixin;
use Mpietrucha\Laravel\Essentials\Macro\MixinAnalyzer;
use Mpietrucha\Support\Filesystem;
use Mpietrucha\Support\Filesystem\Extension;
use Mpietrucha\Support\Filesystem\Path;

/**
 * @phpstan-import-type MixinTarget from Mixin
 * @phpstan-import-type MixinHandlerCollection from Mixin
 */
class GenerateMixinAnalyzers extends Command
{
    use InteractsWithLint;

    /**
     * @var string
     */
    protected $signature = 'essentials:mixin-analyzers
                            {--directory=phpstan/cache : The output directory for generated analyzer files}
                            {--cwd= : The current working directory used for generating analyzers}';

    /**
     * @var string
     */
    protected $description = 'Generate PHPStan analyzer files for registered mixins';

    public function handle(): void
    {
        $analyzers = Mixin::storage()->map(function (Collection $handlers, string $target) {
            $content = MixinAnalyzer::content($target, $handlers);

            if ($content === null) {
                return null;
            }

            $file = $this->file($target);

            Filesystem::put($file, $content);

            return $file;
        })->filter()->keys();

        if ($analyzers->isEmpty()) {
            $this->warn('No registered mixins found. Register mixins in your service provider before running this command.');

            return;
        }

        $this->lint($analyzers);

        $analyzers->each(function (string $analyzer) {
            $this->components->task($analyzer);
        });

        sprintf('%s mixin analyzer(s) generated in [%s].', $analyzers->count(), $this->directory()) |> $this->info(...);
    }

    protected function file(string $target): string
    {
        $name = Path::name($target);

        return Path::build(Extension::set($name, 'php'), $this->directory());
    }

    protected function directory(): string
    {
        /** @var string $directory */
        $directory = $this->option('directory');

        /** @var string $cwd */
        $cwd = $this->option('cwd') ?? Path::directory(__DIR__, 2);

        return Path::build($directory, $cwd);
    }
}
