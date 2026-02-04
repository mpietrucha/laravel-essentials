<?php

namespace Mpietrucha\Laravel\Package\Commands;

use Illuminate\Console\Command;
use Mpietrucha\Laravel\Package\Commands\Concerns\InteractsWithMixins;
use Mpietrucha\Utility\Collection;
use Mpietrucha\Utility\Enumerable\Contracts\EnumerableInterface;
use Mpietrucha\Utility\Instance\Path;
use Mpietrucha\Utility\Str;

/**
 * @phpstan-import-type MixinCollection from \Mpietrucha\Laravel\Package\Mixin
 */
class GenerateMixinAnalyzers extends Command
{
    use InteractsWithMixins;

    /**
     * @var string
     */
    protected $signature = 'mixins:analyzers
                            {--directory=analyze/src : Output directory for analyzers}
                            {--namespace=App\Analyze : Namespace of analyzer class}';

    /**
     * @var string
     */
    protected $description = 'Generate PHPStan analyzer files for registered mixins';

    /**
     * @param  MixinCollection  $mixins
     */
    protected function generate(string $destination, Collection $mixins): ?string
    {
        $namespace = $this->option('namespace');

        $class = Path::name($destination);

        $destination = Path::canonicalize($destination);

        return $this->hydrate($namespace, $class, $destination, $this->mixins($mixins));
    }

    protected function stub(): string
    {
        return '<?php namespace %s; class %s extends %s { %s }';
    }

    /**
     * @param  MixinCollection  $mixins
     */
    protected function mixins(Collection $mixins): string
    {
        $mixins = $mixins->pipeThrough([
            fn (EnumerableInterface $mixins) => Path::canonicalize(...) |> $mixins->map(...),
            fn (EnumerableInterface $mixins) => Value::pipe('use %s;', Str::sprintf(...)) |> $mixins->map(...),
        ]);

        return Str::eol() |> $mixins->join(...);
    }
}
