<?php

namespace Mpietrucha\Laravel\Essentials\Enums\Concerns;

use Mpietrucha\Laravel\Essentials\Events\CurrencyUpdated;
use Mpietrucha\Support\Enums\Contracts\CurrencyInterface;

/**
 * @phpstan-require-implements CurrencyInterface
 */
trait InteractsWithCurrency
{
    use \Mpietrucha\Support\Enums\Concerns\InteractsWithCurrency;

    public static function key(): string
    {
        return 'app.currency';
    }

    public static function get(): static
    {
        /** @var null|string $currency */
        $currency = static::key() |> config(...);

        if ($currency === null) {
            return static::default();
        }

        return static::from($currency);
    }

    public static function set(string $currency): static
    {
        $currency = static::from($currency);

        $previous = static::get();

        config([
            static::key() => $currency->value(),
        ]);

        CurrencyUpdated::dispatch($currency, $previous);

        return $currency;
    }
}
