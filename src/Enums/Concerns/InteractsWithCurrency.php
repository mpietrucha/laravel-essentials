<?php

namespace Mpietrucha\Laravel\Essentials\Enums\Concerns;

use Mpietrucha\Laravel\Essentials\Locale\Currency;

/**
 * @phpstan-require-implements \Mpietrucha\Utility\Enums\Contracts\InteractsWithEnumInterface
 */
trait InteractsWithCurrency
{
    public static function current(): static
    {
        return Currency::get() |> static::from(...);
    }
}
