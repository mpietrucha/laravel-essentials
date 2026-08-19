<?php

namespace Mpietrucha\Laravel\Essentials\Locale\Enums\Concerns;

use Brick\Math\RoundingMode;
use Brick\Money\Context;
use Brick\Money\Money;
use Mpietrucha\Laravel\Essentials\Locale\Enums\Contracts\CurrencyInterface;
use Mpietrucha\Laravel\Essentials\Locale\Events\CurrencyUpdated;
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

    public static function set(mixed $currency): static
    {
        $currency = static::build($currency);

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

    public function money(mixed $money, ?Context $context = null, ?RoundingMode $roundingMode = null): Money
    {
        return MoneyFactory::from($money, $this, $context, $roundingMode);
    }

    public function convert(mixed $money, mixed $currency, ?Context $context = null, ?RoundingMode $roundingMode = null): Money
    {
        return CurrencyConverter::convert($money, $this, $currency, $context, $roundingMode);
    }

    protected static function config(): string
    {
        return 'laravel-essentials.locale.currency';
    }
}
