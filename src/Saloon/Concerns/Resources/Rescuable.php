<?php

namespace Mpietrucha\Laravel\Essentials\Saloon\Concerns\Resources;

use Saloon\Http\BaseResource;
use Saloon\Http\Request;
use Saloon\Http\Response;

/**
 * @phpstan-require-extends BaseResource
 */
trait Rescuable
{
    protected function rescueRequest(Request $request): ?Response
    {
        return rescue(fn (): Response => $this->connector->send($request));
    }

    protected function rescueDto(?Response $response, mixed $rescue = null): mixed
    {
        if (! $response instanceof Response) {
            return $rescue;
        }

        if ($response->failed()) {
            return $rescue;
        }

        return rescue($response->dto(...), $rescue);
    }

    protected function rescueToDto(Request $request, mixed $rescue = null): mixed
    {
        $response = $this->rescueRequest($request);

        return $this->rescueDto($response, $rescue);
    }
}
