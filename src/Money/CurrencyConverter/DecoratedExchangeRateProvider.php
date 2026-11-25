<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Money\CurrencyConverter;

use Brick\Math\BigNumber;
use Brick\Money\CurrencyConverter;
use Brick\Money\ExchangeRateProvider;
use Mpietrucha\Laravel\Essentials\Money\Exchanger\FallbackExchangerService;
use Mpietrucha\Support\Concerns\Makeable;

class DecoratedExchangeRateProvider implements ExchangeRateProvider
{
    use Makeable;

    public function __construct(private ExchangeRateProvider $exchangeRateProvider)
    {
    }

    public static function buildCurrencyConverter(ExchangeRateProvider $exchangeRateProvider): CurrencyConverter
    {
        $exchangeRateProvider = static::make($exchangeRateProvider);

        return new CurrencyConverter($exchangeRateProvider);
    }

    public function getExchangeRate(string $sourceCurrency, string $targetCurrency): BigNumber|float|int|string
    {
        $rate = $this->exchangeRateProvider->getExchangeRate($sourceCurrency, $targetCurrency);

        FallbackExchangerService::setExchangeRate(
            $sourceCurrency,
            $targetCurrency,
            $rate instanceof BigNumber ? $rate->toString() : (string) $rate,
        );

        return $rate;
    }
}
