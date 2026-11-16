<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Saloon\Concerns\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * @phpstan-require-extends Request
 */
trait AsQuery
{
    protected Method $method = Method::QUERY;
}
