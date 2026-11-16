<?php

namespace Mpietrucha\Laravel\Essentials\Saloon\Requests;

use Mpietrucha\Laravel\Essentials\Saloon\Concerns\Requests\AsPatch;
use Saloon\Http\Request;

abstract class PatchRequest extends Request
{
    use AsPatch;
}
