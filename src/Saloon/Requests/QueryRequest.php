<?php

namespace Mpietrucha\Laravel\Essentials\Saloon\Requests;

use Mpietrucha\Laravel\Essentials\Saloon\Concerns\Requests\AsQuery;
use Saloon\Http\Request;

abstract class QueryRequest extends Request
{
    use AsQuery;
}
