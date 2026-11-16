<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Saloon;

use Mpietrucha\Laravel\Essentials\Saloon\Concerns\Connectors\HasBaseUrl;
use Saloon\Http\Connector as SaloonConnector;

abstract class Connector extends SaloonConnector
{
    use HasBaseUrl;
}
