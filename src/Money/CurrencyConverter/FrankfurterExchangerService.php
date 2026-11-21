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
use Illuminate\Support\Uri;
use Mpietrucha\Laravel\Essentials\Money\CurrencyConverter\Concerns\CanBeRegistered;
use Mpietrucha\Laravel\Essentials\Money\CurrencyConverter\Concerns\HasIlluminateClientResponse;
use Mpietrucha\Support\Arr;

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
        return 'frankfurter';
    }

    protected function getLatestExchangeRate(ExchangeRateQuery $exchangeQuery): ExchangeRate
    {
        $currencyPair = $exchangeQuery->getCurrencyPair();

        $url = $this->buildRequestUrl($currencyPair);

        return $this->createExchangeRate($url, $currencyPair);
    }

    protected function getHistoricalExchangeRate(HistoricalExchangeRateQuery $exchangeQuery): ExchangeRate
    {
        $currencyPair = $exchangeQuery->getCurrencyPair();

        $url = $this->buildRequestUrl(
            $currencyPair,
            $exchangeQuery->getDate()->format('Y-m-d')
        );

        return $this->createExchangeRate($url, $currencyPair);
    }

    private function createExchangeRate(string $url, CurrencyPair $currencyPair): ExchangeRate
    {
        $response = $this->getIlluminateClientResponse($url)->throw();

        $date = $response->json('{first}.date');

        $rate = $response->json('{first}.rate');

        if (! is_string($date) || ! is_numeric($rate)) {
            throw new UnsupportedCurrencyPairException($currencyPair, $this);
        }

        return $this->createRate(
            $currencyPair,
            (float) $rate,
            new DateTimeImmutable($date, new DateTimeZone('UTC')),
        );
    }

    private function buildRequestUrl(CurrencyPair $currencyPair, ?string $date = null): string
    {
        $options = $this->options;

        $url = Arr::string($options, 'base_url', 'https://api.frankfurter.dev') |> Uri::of(...);

        $url = $url->withPath('v2/rates');

        return (string) $url->withQuery([
            'base' => $currencyPair->getBaseCurrency(),
            'quotes' => $currencyPair->getQuoteCurrency(),
            'date' => $date,
            'providers' => Arr::get($options, 'providers'),
        ]);
    }
}
