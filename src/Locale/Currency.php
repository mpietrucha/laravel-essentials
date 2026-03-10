<?php

namespace Mpietrucha\Laravel\Essentials\Locale;

use Mpietrucha\Laravel\Essentials\Events\CurrencyUpdated;
use Mpietrucha\Laravel\Essentials\Locale\Concerns\InteractsWithEnum;
use Mpietrucha\Laravel\Essentials\Locale\Contracts\InteractsWithEnumInterface;
use Mpietrucha\Utility\Type;

abstract class Currency implements InteractsWithEnumInterface
{
    use InteractsWithEnum;

    public static function config(): string
    {
        return 'app.currency';
    }

    public static function get(): ?string
    {
        $currency = static::config() |> config(...);

        $enum = static::enum();

        if (Type::null($enum)) {
            return $currency;
        }

        $currency = match (true) {
            Type::null($currency) => $enum::default(),
            default => $enum::from($currency)
        };

        return $currency->value();
    }

    public static function set(string $currency): void
    {
        $previous = static::get();

        $config = static::config();

        config()->set($config, $currency);

        CurrencyUpdated::dispatch($currency, $previous);
    }

    protected static function hydrate(): string
    {
        /** @phpstan-ignore class.notFound */
        return \App\Enums\Currency::class;
    }
}
