<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Saloon\Requests;

use Mpietrucha\Laravel\Essentials\Saloon\Concerns\Requests\AsOptions;
use Saloon\Http\Request;

abstract class OptionsRequest extends Request
{
    use AsOptions;
}
