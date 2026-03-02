<?php

namespace Mpietrucha\Laravel\Essentials\Concerns;

use Mpietrucha\Utility\Collection;
use Mpietrucha\Utility\Context;
use Mpietrucha\Utility\Enumerable\Contracts\EnumerableInterface;
use Mpietrucha\Utility\Type;

/**
 * @internal
 *
 * @phpstan-type ItemCollection \Mpietrucha\Utility\Collection<array-key, mixed>
 * @phpstan-type MapCollection \Mpietrucha\Utility\Collection<class-string, ItemCollection>
 */
trait InteractsWithMap
{
    /**
     * @var null|MapCollection
     */
    protected static ?EnumerableInterface $map = null;

    /**
     * @return MapCollection
     */
    public static function map(): EnumerableInterface
    {
        return static::$map ??= Collection::create();
    }

    protected static function store(string $name, mixed $value, ?string $key = null): void
    {
        if (Context::web()) {
            return;
        }

        $bucket = Collection::create(...);

        static::map()->getOrPut($name, $bucket)->prepend($value, $key);
    }
}
