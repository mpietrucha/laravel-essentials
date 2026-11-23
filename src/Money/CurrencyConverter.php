<?php

namespace Mpietrucha\Laravel\Essentials\Money;

use Brick\Math\RoundingMode;
use Brick\Money\Context;
use Brick\Money\CurrencyConverter as BrickCurrencyConverter;
use Brick\Money\ExchangeRateProvider;
use Brick\Money\Money;
use Mpietrucha\Laravel\Essentials\Money\CurrencyConverter\SwapExchangeRateProvider;
use Throwable;

abstract class CurrencyConverter
{
    protected static ?ExchangeRateProvider $exchangeRateProvider = null;

    protected static ?BrickCurrencyConverter $brickCurrencyConverter = null;

    public static function getExchangeRateProvider(): ExchangeRateProvider
    {
        if (static::$exchangeRateProvider instanceof ExchangeRateProvider) {
            return static::$exchangeRateProvider;
        }

        return static::$exchangeRateProvider = resolve(SwapExchangeRateProvider::class);
    }

    public static function getBrickCurrencyConverter(): BrickCurrencyConverter
    {
        if (static::$brickCurrencyConverter instanceof BrickCurrencyConverter) {
            return static::$brickCurrencyConverter;
        }

        $exchangeRateProvider = static::getExchangeRateProvider();

        return static::$brickCurrencyConverter = new BrickCurrencyConverter($exchangeRateProvider);
    }

    public static function convert(mixed $money, mixed $targetCurrency = null, mixed $sourceCurrency = null, ?Context $context = null, ?RoundingMode $roundingMode = null): Money
    {
        $roundingMode ??= MoneyFactory::getDefaultRoundingMode();

        $money = MoneyFactory::from($money, $sourceCurrency, $context, $roundingMode);

        $targetCurrency = CurrencyFactory::from($targetCurrency);

        return static::getBrickCurrencyConverter()->convert($money, $targetCurrency, $context, $roundingMode);
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
