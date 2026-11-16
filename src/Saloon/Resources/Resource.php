<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Saloon\Resources;

use Mpietrucha\Laravel\Essentials\Saloon\Concerns\Resources\Rescuable;
use Mpietrucha\Support\Concerns\Makeable;
use Saloon\Http\BaseResource;

abstract class Resource extends BaseResource
{
    use Makeable;
    use Rescuable;
}
