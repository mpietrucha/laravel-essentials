<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Money\CurrencyConverter;

use DateTimeImmutable;
use DateTimeZone;
use Exchanger\Contract\CurrencyPair;
use Exchanger\Contract\ExchangeRate;
use Exchanger\Contract\ExchangeRateQuery;
use Exchanger\Contract\HistoricalExchangeRateQuery;
use Exchanger\Exception\UnsupportedCurrencyPairException;
use Exchanger\Service\HttpService;
use Exchanger\Service\SupportsHistoricalQueries;
use Mpietrucha\Laravel\Essentials\Money\CurrencyConverter\Concerns\CanBeRegistered;
use Mpietrucha\Laravel\Essentials\Money\CurrencyConverter\Concerns\HasIlluminateClientResponse;
use Mpietrucha\Laravel\Essentials\Qualifier;

final class FrankfurterExchangerService extends HttpService
{
    use CanBeRegistered;
    use HasIlluminateClientResponse;
    use SupportsHistoricalQueries;

    public function supportQuery(ExchangeRateQuery $exchangeQuery): bool
    {
        return true;
    }

    public function getName(): string
    {
        return 'frankfuter';
    }

    protected function getLatestExchangeRate(ExchangeRateQuery $exchangeQuery): ExchangeRate
    {
        $currencyPair = $exchangeQuery->getCurrencyPair();

        $url = sprintf(
            'https://api.frankfurter.dev/v1/latest?base=%s&symbols=%s',
            $currencyPair->getBaseCurrency(),
            $currencyPair->getQuoteCurrency(),
        );

        return $this->createExchangeRate($url, $currencyPair);
    }

    protected function getHistoricalExchangeRate(HistoricalExchangeRateQuery $exchangeQuery): ExchangeRate
    {
        $currencyPair = $exchangeQuery->getCurrencyPair();

        $url = sprintf(
            'https://api.frankfurter.dev/v1/%s?base=%s&symbols=%s',
            $exchangeQuery->getDate()->format('Y-m-d'),
            $currencyPair->getBaseCurrency(),
            $currencyPair->getQuoteCurrency(),
        );

        return $this->createExchangeRate($url, $currencyPair);
    }

    private function createExchangeRate(string $url, CurrencyPair $currencyPair): ExchangeRate
    {
        $response = $this->getIlluminateClientResponse($url)->throw();

        $date = $response->json('date');

        $rate = Qualifier::build($currencyPair->getQuoteCurrency(), 'rates') |> $response->json(...);

        if (! is_string($rate) || ! is_string($date)) {
            throw new UnsupportedCurrencyPairException($currencyPair, $this);
        }

        return $this->createRate(
            $currencyPair,
            (float) $rate,
            new DateTimeImmutable($date, new DateTimeZone('UTC')),
        );
    }
}
