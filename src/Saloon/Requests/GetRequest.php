<?php

namespace Mpietrucha\Laravel\Essentials\Saloon\Requests;

use Mpietrucha\Laravel\Essentials\Saloon\Concerns\Requests\AsGet;
use Saloon\Http\Request;

abstract class GetRequest extends Request
{
    use AsGet;
}
