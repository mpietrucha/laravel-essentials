<?php

namespace Mpietrucha\Laravel\Essentials\Saloon\Concerns\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * @phpstan-require-extends Request
 */
trait AsConnect
{
    protected Method $method = Method::CONNECT;
}
