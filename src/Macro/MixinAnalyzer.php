<?php

namespace Mpietrucha\Laravel\Essentials\Macro;

use Illuminate\Support\Collection;
use Mpietrucha\Support\ClassNamespace;
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

        return $indicator . md5($indicator) . ClassNamespace::delimiter();
    }

    /**
     * @param  MixinTarget  $target
     */
    public static function namespace(string $target): string
    {
        $namespace = ClassNamespace::parent($target);

        return ClassNamespace::join(static::indicator(), $namespace);
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

        $namespace = static::namespace($target);

        $class = ClassNamespace::name($target);

        $target = ClassNamespace::canonicalize($target);

        return sprintf(static::stub(), $namespace, $class, $target, $uses);
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

            return sprintf('use %s;', ClassNamespace::canonicalize($handler));
        })->filter();

        return Str::eol() |> $handlers->join(...) |> Str::nullWhenEmpty(...);
    }
}
