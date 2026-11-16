<?php

namespace Mpietrucha\Laravel\Essentials\Saloon\Concerns\Connectors;

use Mpietrucha\Support\Exception\BadMethodCallException;
use Saloon\Http\Connector;

/**
 * @phpstan-require-extends Connector
 */
trait HasBaseUrl
{
    protected ?string $baseUrl = null;

    public function withBaseUrl(string $baseUrl): static
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    public function resolveBaseUrl(): string
    {
        return $this->baseUrl ?? BadMethodCallException::throw('Body of a `resolveBaseUrl` method must be specified');
    }
}
