<?php

namespace Mpietrucha\Laravel\Essentials\Locale;

use Mpietrucha\Laravel\Essentials\Events\CurrencyUpdated;

class Currency
{
    public static function config(): string
    {
        return 'app.currency';
    }

    public static function get(): ?string
    {
        return static::config() |> config(...);
    }

    public static function set(string $currency): void
    {
        $previous = static::get();

        $config = static::config();

        config()->set($config, $currency);

        CurrencyUpdated::dispatch($currency, $previous);
    }
}
