<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Saloon\Requests;

use Mpietrucha\Laravel\Essentials\Saloon\Concerns\Requests\AsConnect;
use Saloon\Http\Request;

abstract class ConnectRequest extends Request
{
    use AsConnect;
}
