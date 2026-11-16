<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Saloon\Requests;

use Mpietrucha\Laravel\Essentials\Saloon\Concerns\Requests\AsPut;
use Saloon\Http\Request;

abstract class PutRequest extends Request
{
    use AsPut;
}
