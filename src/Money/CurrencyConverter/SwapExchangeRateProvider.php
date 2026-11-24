<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Money\CurrencyConverter;

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

    public function getExchangeRate(string $sourceCurrency, string $targetCurrency): string
    {
        $currencyPair = sprintf('%s/%s', $sourceCurrency, $targetCurrency);

        try {
            return (string) $this->swap->latest($currencyPair)->getValue();
        } catch (Throwable $throwable) {
            throw CurrencyConversionException::exchangeRateNotAvailable($sourceCurrency, $targetCurrency, previous: $throwable);
        }
    }
}
