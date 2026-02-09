<?php

namespace Mpietrucha\Laravel\Essentials\Commands;

use Illuminate\Console\Command;
use Mpietrucha\Laravel\Essentials\Commands\Concerns\InteractsWithLint;
use Mpietrucha\Laravel\Essentials\Mixin;
use Mpietrucha\Laravel\Essentials\Package\Context;
use Mpietrucha\Utility\Collection;
use Mpietrucha\Utility\Enumerable\Contracts\EnumerableInterface;
use Mpietrucha\Utility\Filesystem;
use Mpietrucha\Utility\Filesystem\Extension;
use Mpietrucha\Utility\Filesystem\Path;
use Mpietrucha\Utility\Instance\Path as FQN;
use Mpietrucha\Utility\Str;
use Mpietrucha\Utility\Type;
use Mpietrucha\Utility\Value;

/**
 * @phpstan-import-type MixinCollection from \Mpietrucha\Laravel\Essentials\Mixin
 */
class GenerateMixinAnalyzers extends Command
{
    use InteractsWithLint;

    /**
     * @var string
     */
    protected $signature = 'mixin:analyzers';

    /**
     * @var string
     */
    protected $description = 'Generate PHPStan analyzer files for registered mixins';

    public function handle(): void
    {
        $files = Mixin::map()->pipeThrough([
            fn (EnumerableInterface $map) => $this->build(...) |> $map->mapWithKeys(...),
            fn (EnumerableInterface $map) => $map->filter(),
            fn (EnumerableInterface $map) => Filesystem::put(...) |> $map->eachKeys(...),
            fn (EnumerableInterface $map) => $map->keys(),
        ]);

        if ($files->isEmpty()) {
            $this->warn('No generated mixins found.');

            return;
        }

        $this->lint($files);

        $this->info('Mixin analyzers generated successfully.');

        $files->each(fn (string $file) => $this->components->task($file));
    }

    /**
     * @param  MixinCollection  $mixins
     * @param  class-string  $destination
     * @return null|array<string, string>
     */
    protected function build(Collection $mixins, string $destination): ?array
    {
        $content = $this->generate($destination, $mixins);

        if (Type::null($content)) {
            return null;
        }

        return [$this->file($destination) => $content];
    }

    /**
     * @param  class-string  $destination
     * @param  MixinCollection  $mixins
     */
    protected function generate(string $destination, Collection $mixins): ?string
    {
        $mixins = $this->mixins($mixins);

        if (Type::null($mixins)) {
            return null;
        }

        $namespace = FQN::namespace($destination);

        $destination = FQN::canonicalize($destination);

        $class = FQN::name($destination);

        return $this->hydrate($namespace, $class, $destination, $mixins);
    }

    protected function hydrate(mixed ...$arguments): string
    {
        $stub = $this->stub();

        return Str::sprintf($stub, ...$arguments);
    }

    protected function stub(): string
    {
        return '<?php namespace %s; class %s extends %s { %s }';
    }

    /**
     * @param  MixinCollection  $mixins
     */
    protected function mixins(Collection $mixins): ?string
    {
        $use = Value::pipe('use %s;', Str::sprintf(...));

        $mixins = $mixins->pipeThrough([
            fn (EnumerableInterface $mixins) => Type::string(...) |> $mixins->filter(...),
            fn (EnumerableInterface $mixins) => FQN::canonicalize(...) |> $mixins->map(...),
            fn (EnumerableInterface $mixins) => $mixins->map($use),
        ]);

        return Str::eol() |> $mixins->join(...) |> Str::nullWhenEmpty(...);
    }

    protected function file(string $destination): string
    {
        $directory = Path::build('phpstan/cache', Context::directory());

        $file = Path::name($destination);

        return Path::build(Extension::set($file, 'php'), $directory);
    }
}
