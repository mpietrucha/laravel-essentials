<?php

namespace Mpietrucha\Laravel\Essentials\Money\Exchanger;

use DateTimeImmutable;
use Exchanger\Contract\ExchangeRate;
use Exchanger\Contract\ExchangeRateQuery;
use Exchanger\Contract\HistoricalExchangeRateQuery;
use Exchanger\CurrencyPair;
use Exchanger\Exception\UnsupportedCurrencyPairException;
use Exchanger\Service\Service;
use Mpietrucha\Laravel\Essentials\Money\Exchanger\Concerns\CanBeRegistered;
use Mpietrucha\Support\Arr;
use Mpietrucha\Support\Str;

class CachedExchangerService extends Service
{
    use CanBeRegistered;

    public static function setExchangeRate(string $sourceCurrency, string $targetCurrency, string $rate): void
    {
        $cacheKey = static::getCacheKey($sourceCurrency, $targetCurrency);

        $date = now()->utc()->toIso8601String();

        cache()->set($cacheKey, [$rate, $date]);
    }

    public function supportQuery(ExchangeRateQuery $exchangeQuery): bool
    {
        return ! $exchangeQuery instanceof HistoricalExchangeRateQuery;
    }

    public function getExchangeRate(ExchangeRateQuery $exchangeQuery): ExchangeRate
    {
        $cacheKey = static::getCacheKey($currencyPair = $exchangeQuery->getCurrencyPair());

        /** @var array<mixed> $latestExchange */
        $latestExchange = cache()->get($cacheKey, []);

        $date = Arr::tryNotEmptyString($latestExchange, 1);

        $rate = Arr::tryNotEmptyString($latestExchange, 0);

        if (! is_string($date) || ! is_numeric($rate)) {
            cache()->forget($cacheKey);

            throw new UnsupportedCurrencyPairException($currencyPair, $this);
        }

        return $this->createRate(
            $currencyPair,
            (float) $rate,
            new DateTimeImmutable($date),
        );
    }

    protected static function getCacheKey(string $sourceCurrency, ?string $targetCurrency = null): string
    {
        $prefix = get_called_class() |> Str::kebab(...);

        $currencyPair = match (true) {
            $targetCurrency === null => CurrencyPair::createFromString($sourceCurrency),
            default => new CurrencyPair($sourceCurrency, $targetCurrency),
        };

        return sprintf('%s:%s', $prefix, (string) $currencyPair);
    }
}
