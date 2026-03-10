<?php

namespace Mpietrucha\Laravel\Essentials\Enums\Concerns;

use Mpietrucha\Laravel\Essentials\Locale;

/**
 * @phpstan-require-implements \Mpietrucha\Utility\Enums\Contracts\InteractsWithEnumInterface
 */
trait InteractsWithLocale
{
    public static function current(): static
    {
        return Locale::get() |> static::from(...);
    }
}
