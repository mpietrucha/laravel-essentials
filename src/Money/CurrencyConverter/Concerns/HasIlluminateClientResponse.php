<?php

namespace Mpietrucha\Laravel\Essentials\Money\CurrencyConverter\Concerns;

use Exchanger\Contract\ExchangeRateService;
use Illuminate\Http\Client\Response;

/**
 * @phpstan-require-implements ExchangeRateService
 */
trait HasIlluminateClientResponse
{
    /**
     * @param  array<string>  $headers
     */
    public function getIlluminateClientResponse(string $url, array $headers = []): Response
    {
        $response = $this->getResponse($url, $headers);

        return new Response($response);
    }
}
