<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Saloon\Requests;

use Mpietrucha\Laravel\Essentials\Saloon\Concerns\Requests\AsTrace;
use Saloon\Http\Request;

abstract class TraceRequest extends Request
{
    use AsTrace;
}
