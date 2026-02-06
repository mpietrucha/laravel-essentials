<?php

namespace Mpietrucha\Laravel\Essentials\Concerns;

use Mpietrucha\Utility\Enumerable\Contracts\EnumerableInterface;

/**
 * @internal
 *
 * @phpstan-require-extends \Mpietrucha\Laravel\Essentials\Macro|\Mpietrucha\Laravel\Essentials\Mixin
 */
trait InteractsWithMap
{
    public static function map(): EnumerableInterface
    {

    }

    protected static function store(string $name, mixed $value, ?string $key = null): void
    {

    }
}
