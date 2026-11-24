<?php

namespace Mpietrucha\Laravel\Essentials\Money\CurrencyConverter\Concerns;

use Exchanger\Contract\ExchangeRateService;
use Illuminate\Http\Client\Response;

/**
 * @phpstan-require-implements ExchangeRateService
 */
trait HasDecoratedResponse
{
    /**
     * @param  array<string>  $headers
     */
    public function getDecoratedResponse(string $url, array $headers = []): Response
    {
        $response = $this->getResponse($url, $headers);

        return new Response($response);
    }
}
