<?php

namespace Mpietrucha\Laravel\Essentials\Enums\Concerns;

use Brick\Math\RoundingMode;
use Brick\Money\Context;
use Brick\Money\Money;
use Mpietrucha\Laravel\Essentials\Enums\Contracts\CurrencyInterface;
use Mpietrucha\Laravel\Essentials\Events\CurrencyUpdated;
use Mpietrucha\Laravel\Essentials\Money\CurrencyConverter;
use Mpietrucha\Laravel\Essentials\Money\MoneyFactory;
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

        event(new CurrencyUpdated($currency, $previous));

        return $currency;
    }

    public function symbol(): string
    {
        return $this->code() |> Currencies::getSymbol(...);
    }

    public function money(mixed $amount, ?Context $context = null, ?RoundingMode $roundingMode = null): Money
    {
        return MoneyFactory::from($amount, $this, $context, $roundingMode);
    }

    public function convert(mixed $amount, mixed $currency, ?Context $context = null, ?RoundingMode $roundingMode = null): Money
    {
        return CurrencyConverter::convert($amount, $this, $currency, $context, $roundingMode);
    }

    protected static function config(): string
    {
        return 'app.currency';
    }
}
