<?php

namespace Mpietrucha\Laravel\Package\Commands\Concerns;

use Mpietrucha\Laravel\Package\Commands\GenerateMixinAnalyzers;
use Mpietrucha\Laravel\Package\Mixin;
use Mpietrucha\Utility\Collection;
use Mpietrucha\Utility\Enumerable\Contracts\EnumerableInterface;
use Mpietrucha\Utility\Filesystem;
use Mpietrucha\Utility\Filesystem\Extension;
use Mpietrucha\Utility\Filesystem\Path;
use Mpietrucha\Utility\Str;
use Mpietrucha\Utility\Type;

/**
 * @phpstan-require-extends \Illuminate\Console\Command
 *
 * @phpstan-import-type MixinCollection from \Mpietrucha\Laravel\Package\Mixin
 */
trait InteractsWithMixins
{
    public function handle(): void
    {
        Mixin::registry()->pipeThrough([
            fn (EnumerableInterface $registry) => $this->build(...) |> $registry->mapWithKeys(...),
            fn (EnumerableInterface $mixins) => $mixins->filter(),
            fn (EnumerableInterface $mixins) => Filesystem::put(...) |> $mixins->each(...),
        ]);
    }

    /**
     * @param  MixinCollection  $mixins
     * @return null|array<string, string>
     */
    protected function build(string $destination, Collection $mixins): ?array
    {
        $content = $this->generate($destination, $mixins);

        if (Type::null($content)) {
            return null;
        }

        return [$this->file($destination) => $content];
    }

    /**
     * @param  MixinCollection  $mixins
     */
    protected function generate(string $destination, Collection $mixins): ?string
    {
        return null;
    }

    protected function hydrate(mixed ...$arguments): string
    {
        $stub = $this->stub();

        return Str::sprintf($stub, ...$arguments);
    }

    protected function stub(): string
    {
        return Str::none();
    }

    protected function file(string $destination): string
    {
        $directory = $this->directory();

        $file = Extension::set(Path::name($destination), $this->extension());

        return Path::build($file, $directory);
    }

    protected function directory(): string
    {
        return $this->option('directory') |> base_path(...);
    }

    protected function extension(): string
    {
        return match (true) {
            $this instanceof GenerateMixinAnalyzers => 'php',
            default => 'stub'
        };
    }
}
