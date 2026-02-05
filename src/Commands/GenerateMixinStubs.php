<?php

namespace Mpietrucha\Laravel\Package\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Traits\Macroable;
use Mpietrucha\Laravel\Package\Commands\Concerns\InteractsWithMixins;
use Mpietrucha\Utility\Collection;
use Mpietrucha\Utility\Concerns\Compatible;
use Mpietrucha\Utility\Contracts\CompatibleInterface;
use Mpietrucha\Utility\Enumerable\Contracts\EnumerableInterface;
use Mpietrucha\Utility\Filesystem;
use Mpietrucha\Utility\Instance;
use Mpietrucha\Utility\Instance\Path;
use Mpietrucha\Utility\Str;
use Mpietrucha\Utility\Stringable;

/**
 * @phpstan-import-type MixinCollection from \Mpietrucha\Laravel\Package\Mixin
 */
class GenerateMixinStubs extends Command implements CompatibleInterface
{
    use Compatible, InteractsWithMixins;

    /**
     * @var string
     */
    protected $signature = 'mixin:stubs';

    /**
     * @var string
     */
    protected $description = 'Generate PHPStan stub file for registered mixins';

    protected function done(): void
    {
        $this->info('Mixin stubs generated successfully.');
    }

    /**
     * @param  MixinCollection  $mixins
     */
    protected function generate(string $destination, Collection $mixins): ?string
    {
        if (static::incompatible($destination)) {
            return null;
        }

        $namespace = Path::namespace($destination);

        $class = Path::name($destination);

        return $this->hydrate($namespace, $this->docblock($mixins), $class);
    }

    protected function stub(): string
    {
        return '<?php namespace %s; %sclass %s {}';
    }

    /**
     * @param  MixinCollection  $mixins
     */
    protected function docblock(Collection $mixins): string
    {
        $mixins = $this->signatures(...) |> $mixins->map(...);

        $eol = Str::eol();

        return $mixins->unshift($eol, '/**')->push('*/', $eol)->join($eol);
    }

    protected function signatures(string $mixin): string
    {
        $content = Instance::file($mixin) |> Filesystem::get(...) |> Str::of(...);

        $signatures = $content->lines()->pipeThrough([
            fn (EnumerableInterface $lines) => $lines->mapToStringables(),
            fn (EnumerableInterface $lines) => $lines->map->trim(),
            fn (EnumerableInterface $lines) => $lines->filter->is('*public*function*'),
            fn (EnumerableInterface $lines) => $this->signature(...) |> $lines->map(...),
        ]);

        return Str::eol() |> $signatures->join(...);
    }

    protected function signature(Stringable $method): string
    {
        $declaration = $method->explode(':')->mapToStringables();

        $return = $declaration->last() ?? Str::none();

        /** @var \Mpietrucha\Utility\Stringable $body */
        $body = $declaration->first();

        return $body->remove(['public', 'function'])->prepend($return)->prepend('* @method')->squish();
    }

    protected static function defaultDirectory(): string
    {
        return 'phpstan/stubs';
    }

    protected static function compatibility(string $destination): mixed
    {
        return Instance::traits($destination)->doesntContain(Macroable::class);
    }
}
