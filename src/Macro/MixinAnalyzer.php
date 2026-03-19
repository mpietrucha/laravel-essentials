<?php

namespace Mpietrucha\Laravel\Essentials\Macro;

use Illuminate\Support\Collection;
use Mpietrucha\Support\Instance\Path;
use Mpietrucha\Support\Str;

/**
 * @phpstan-import-type MixinTarget from Mixin
 * @phpstan-import-type MixinHandlerCollection from Mixin
 */
abstract class MixinAnalyzer
{
    public static function stub(): string
    {
        return '<?php namespace %s; class %s extends %s { %s }';
    }

    public static function indicator(): string
    {
        $indicator = 'Analyzers';

        return $indicator . md5($indicator) . Path::delimiter();
    }

    /**
     * @param  MixinTarget  $target
     */
    public static function namespace(string $target): string
    {
        $namespace = Path::namespace($target);

        $indicator = static::indicator();

        return Path::join($indicator, $namespace);
    }

    /**
     * @param  MixinTarget  $target
     * @param  MixinHandlerCollection  $handlers
     */
    public static function content(string $target, Collection $handlers): ?string
    {
        $uses = static::uses($handlers);

        if ($uses === null) {
            return null;
        }

        $stub = static::stub();

        $namespace = static::namespace($target);

        $class = Path::name($target);

        $target = Path::canonicalize($target);

        return sprintf($stub, $namespace, $class, $target, $uses);
    }

    /**
     * @param  MixinHandlerCollection  $handlers
     */
    protected static function uses(Collection $handlers): ?string
    {
        $handlers = $handlers->map(static function (object|string $handler): ?string {
            if (is_object($handler)) {
                return null;
            }

            return sprintf('use %s;', Path::canonicalize($handler));
        })->filter();

        return Str::eol() |> $handlers->join(...) |> Str::nullWhenEmpty(...);
    }
}
