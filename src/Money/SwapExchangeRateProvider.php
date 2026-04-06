<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Money;

use Brick\Money\Exception\CurrencyConversionException;
use Brick\Money\ExchangeRateProvider;
use Mpietrucha\Support\Concerns\Makeable;
use Swap\Swap;
use Throwable;

final readonly class SwapExchangeRateProvider implements ExchangeRateProvider
{
    use Makeable;

    public function __construct(private Swap $swap)
    {
    }

    public function getExchangeRate(string $sourceCurrencyCode, string $targetCurrencyCode): float
    {
        $currencyPair = sprintf('%s/%s', $sourceCurrencyCode, $targetCurrencyCode);

        try {
            return $this->swap->latest($currencyPair)->getValue();
        } catch (Throwable) {
            throw CurrencyConversionException::exchangeRateNotAvailable($sourceCurrencyCode, $targetCurrencyCode);
        }
    }
}
