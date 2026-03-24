<?php

namespace Mpietrucha\Laravel\Essentials\Locale;

use Brick\Math\RoundingMode;
use Brick\Money\CurrencyConverter;
use Brick\Money\Money;
use Mpietrucha\Laravel\Essentials\Enums\Contracts\CurrencyInterface;
use Mpietrucha\Support\Exception\InvalidArgumentException;
use Mpietrucha\Support\Exception\RuntimeException;
use Swap\Swap;

abstract class MoneyBuilder
{
    protected static ?CurrencyConverter $currencyConverter = null;

    public static function build(mixed $amount, mixed $currency = null): Money
    {
        $currency = static::currency($currency, $amount);

        if ($currency === null) {
            /** @var Money $amount */
            return $amount;
        }

        return match (true) {
            is_string($amount),
            is_float($amount) => Money::of($amount, $currency),
            is_int($amount) => Money::ofMinor($amount, $currency),
            default => InvalidArgumentException::throw('Unexpected amount of type `%s`', get_debug_type($amount))
        };
    }

    public static function convert(mixed $amount, mixed $targetCurrency = null, mixed $sourceCurrency = null, RoundingMode $roundingMode = RoundingMode::HalfUp): Money
    {
        $targetCurrency = static::currency($targetCurrency);

        $money = static::build($amount, $sourceCurrency);

        return static::getCurrencyConverter()->convert($money, $targetCurrency, roundingMode: $roundingMode);
    }

    /**
     * @return ($amount is null ? string : null|string)
     */
    protected static function currency(mixed $currency = null, mixed $amount = null): ?string
    {
        if ($amount instanceof Money) {
            return null;
        }

        return match (true) {
            $currency instanceof CurrencyInterface => $currency->code(),
            is_string($currency) => $currency,
            is_null($currency) => Currency::get()->code(),
            default => InvalidArgumentException::throw('Unexpected currency of type `%s`', get_debug_type($currency))
        };
    }

    protected static function getCurrencyConverter(): CurrencyConverter
    {
        if (static::$currencyConverter instanceof CurrencyConverter) {
            return static::$currencyConverter;
        }

        $swap = Swap::class;

        if (! app()->bound($swap)) {
            RuntimeException::throw('`%s` must be bound in the service container', $swap);
        }

        $swapExchangeRateProvider = app($swap) |> SwapExchangeRateProvider::make(...);

        return static::$currencyConverter = new CurrencyConverter($swapExchangeRateProvider);
    }
}
