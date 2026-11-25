<?php

namespace Mpietrucha\Laravel\Essentials\Money;

use Brick\Math\RoundingMode;
use Brick\Money\Context;
use Brick\Money\CurrencyConverter as BrickCurrencyConverter;
use Brick\Money\ExchangeRateProvider;
use Brick\Money\Money;
use Mpietrucha\Laravel\Essentials\Money\CurrencyConverter\DecoratedExchangeRateProvider;
use Mpietrucha\Laravel\Essentials\Money\CurrencyConverter\SwapExchangeRateProvider;
use Mpietrucha\Support\Exception\RuntimeException;
use Throwable;

abstract class CurrencyConverter
{
    protected static ?ExchangeRateProvider $exchangeRateProvider = null;

    protected static ?BrickCurrencyConverter $adapter = null;

    public static function setExchangeRateProvider(ExchangeRateProvider $exchangeRateProvider): void
    {
        static::$exchangeRateProvider = $exchangeRateProvider;
    }

    public static function getExchangeRateProvider(): ExchangeRateProvider
    {
        if (static::$exchangeRateProvider instanceof ExchangeRateProvider) {
            return static::$exchangeRateProvider;
        }

        return static::$exchangeRateProvider = resolve(SwapExchangeRateProvider::class);
    }

    public static function adapter(): BrickCurrencyConverter
    {
        if (static::$adapter instanceof BrickCurrencyConverter) {
            return static::$adapter;
        }

        $exchangeRateProvider = static::getExchangeRateProvider();

        return static::$adapter = DecoratedExchangeRateProvider::buildCurrencyConverter($exchangeRateProvider);
    }

    public static function convert(mixed $money, mixed $targetCurrency = null, mixed $sourceCurrency = null, ?Context $context = null, ?RoundingMode $roundingMode = null): Money
    {
        $money = MoneyFactory::from(
            $money,
            $sourceCurrency,
            $context,
            $roundingMode ??= MoneyFactory::getDefaultRoundingMode(),
        );

        $targetCurrency = CurrencyFactory::from($targetCurrency);

        try {
            return static::adapter()->convert(
                $money,
                $targetCurrency,
                $context,
                $roundingMode,
            );
        } catch (Throwable $throwable) {
            report($throwable);

            RuntimeException::usePrevious($throwable);

            RuntimeException::throw('Failed to convert currency');
        }
    }

    public static function tryConvert(mixed $money, mixed $targetCurrency = null, mixed $sourceCurrency = null, ?Context $context = null, ?RoundingMode $roundingMode = null): ?Money
    {
        try {
            return static::convert(
                $money,
                $targetCurrency,
                $sourceCurrency,
                $context,
                $roundingMode
            );
        } catch (Throwable) {
            return null;
        }
    }
}
