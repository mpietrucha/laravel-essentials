<?php

namespace Mpietrucha\Laravel\Essentials\Money;

use Brick\Math\RoundingMode;
use Brick\Money\Context;
use Brick\Money\CurrencyConverter as BrickCurrencyConverter;
use Brick\Money\Money;
use Illuminate\Contracts\Container\BindingResolutionException;
use Mpietrucha\Laravel\Essentials\Enums\Contracts\CurrencyInterface;
use Mpietrucha\Laravel\Essentials\Locale\Currency;
use Mpietrucha\Support\Exception\InvalidArgumentException;
use Mpietrucha\Support\Exception\RuntimeException;
use Swap\Swap;

abstract class CurrencyConverter
{
    protected static ?BrickCurrencyConverter $adapter = null;

    public static function adapter(): BrickCurrencyConverter
    {
        if (static::$adapter instanceof BrickCurrencyConverter) {
            return static::$adapter;
        }

        try {
            $swapExchangeRateProvider = app(SwapExchangeRateProvider::class);
        } catch (BindingResolutionException) {
            RuntimeException::throw('`%s` must be bound in the service container', Swap::class);
        }

        return static::$adapter = new BrickCurrencyConverter($swapExchangeRateProvider);
    }

    /**
     * @return ($amount is null ? string : null|string)
     */
    public static function currency(mixed $currency = null, mixed $amount = null): ?string
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

    public static function convert(mixed $amount, mixed $targetCurrency = null, mixed $sourceCurrency = null, ?Context $context = null, ?RoundingMode $roundingMode = null): Money
    {
        $roundingMode ??= RoundingMode::HalfUp;

        $money = MoneyFactory::from($amount, $sourceCurrency, $context, $roundingMode);

        $targetCurrency = static::currency($targetCurrency);

        return static::adapter()->convert($money, $targetCurrency, $context, $roundingMode);
    }
}
