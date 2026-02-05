<?php

namespace Mpietrucha\Laravel\Package\Commands\Concerns;

use Mpietrucha\Laravel\Package\Commands\GenerateMixinAnalyzers;
use Mpietrucha\Laravel\Package\Mixin;
use Mpietrucha\Utility\Collection;
use Mpietrucha\Utility\Enumerable\Contracts\EnumerableInterface;
use Mpietrucha\Utility\Filesystem;
use Mpietrucha\Utility\Filesystem\Extension;
use Mpietrucha\Utility\Filesystem\Path;
use Mpietrucha\Utility\Normalizer;
use Mpietrucha\Utility\Process;
use Mpietrucha\Utility\Str;
use Mpietrucha\Utility\Stringable;
use Mpietrucha\Utility\Type;
use Symfony\Component\Console\Input\InputOption;

/**
 * @phpstan-require-extends \Illuminate\Console\Command
 *
 * @phpstan-import-type MixinCollection from \Mpietrucha\Laravel\Package\Mixin
 *
 * @phpstan-type FileCollection \Mpietrucha\Utility\Collection<int, string>
 */
trait InteractsWithMixins
{
    public function handle(): void
    {
        $files = Mixin::registry()->pipeThrough([
            fn (EnumerableInterface $files) => $this->build(...) |> $files->mapWithKeys(...),
            fn (EnumerableInterface $files) => $files->filter(),
            fn (EnumerableInterface $files) => $this->transform(...) |> $files->pipe(...),
            fn (EnumerableInterface $files) => Filesystem::put(...) |> $files->eachKeys(...),
            fn (EnumerableInterface $files) => $files->keys(),
        ]);

        $this->lint($files);

        $this->done();

        $files->each(fn (string $file) => $this->components->task($file));
    }

    protected function configure(): void
    {
        parent::configure();

        $this->addOption('lint', null, InputOption::VALUE_NEGATABLE, 'Run lint after generation', static::defaultLint());
        $this->addOption('merge', null, InputOption::VALUE_NEGATABLE, 'Merge all output into a single file', static::defaultMerge());

        $this->addOption('directory', null, InputOption::VALUE_OPTIONAL, 'Output directory', static::defaultDirectory());
        $this->addOption('file', null, InputOption::VALUE_OPTIONAL, 'Output filename (used with --merge)', static::defaultFile());
    }

    protected function done(): void
    {
    }

    /**
     * @param  MixinCollection  $mixins
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

    /**
     * @param  FileCollection  $files
     * @return FileCollection
     */
    protected function transform(Collection $files): Collection
    {
        if ($this->option('merge') |> Normalizer::not(...)) {
            return $files;
        }

        $php = '<?php';

        $content = $files->pipeThrough([
            fn (EnumerableInterface $files) => Str::eol() |> $files->join(...) |> Str::of(...),
            fn (Stringable $content) => $content->remove($php),
            fn (Stringable $content) => $content->start($php),
        ]);

        $file = $this->option('file') |> $this->file(...);

        return [$file => $content] |> Collection::create(...);
    }

    /**
     * @param  FileCollection  $files
     */
    protected function lint(Collection $files): void
    {
        $this->option('lint') && Process::run(['composer', 'lint', ...$files]);
    }

    protected static function defaultLint(): bool
    {
        return true;
    }

    protected static function defaultMerge(): bool
    {
        return true;
    }

    protected static function defaultFile(): string
    {
        return 'Mixins';
    }

    protected static function defaultDirectory(): string
    {
        return Str::none();
    }
}
