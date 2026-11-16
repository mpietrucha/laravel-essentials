<?php

namespace Mpietrucha\Laravel\Essentials\Saloon\Requests;

use Mpietrucha\Laravel\Essentials\Saloon\Concerns\Requests\AsHead;
use Saloon\Http\Request;

abstract class HeadRequest extends Request
{
    use AsHead;
}
