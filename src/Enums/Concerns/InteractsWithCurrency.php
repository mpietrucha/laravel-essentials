<?php

namespace Mpietrucha\Laravel\Essentials\Enums\Concerns;

use Brick\Money\Money;
use Mpietrucha\Laravel\Essentials\Enums\Contracts\CurrencyInterface;
use Mpietrucha\Laravel\Essentials\Events\CurrencyUpdated;
use Mpietrucha\Laravel\Essentials\Locale\MoneyBuilder;
use Symfony\Component\Intl\Currencies;

/**
 * @phpstan-require-implements CurrencyInterface
 */
trait InteractsWithCurrency
{
    use InteractsWithLocale;

    public static function get(): static
    {
        /** @var null|string $currency */
        $currency = static::config() |> config(...);

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
            static::config() => $currency->code(),
        ]);

        CurrencyUpdated::dispatch($currency, $previous);

        return $currency;
    }

    public function symbol(): string
    {
        return $this->code() |> Currencies::getSymbol(...);
    }

    public function money(mixed $amount): Money
    {
        return MoneyBuilder::build($amount, $this);
    }

    public function convert(mixed $amount, mixed $currency): Money
    {
        return MoneyBuilder::convert($amount, $this, $currency);
    }

    protected static function config(): string
    {
        return 'app.currency';
    }
}
