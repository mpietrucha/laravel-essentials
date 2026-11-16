<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Saloon\DataTransferObjects;

use Mpietrucha\Laravel\Essentials\Saloon\Concerns\DataTransferObjects\Buildable;
use Mpietrucha\Support\Concerns\Makeable;

abstract readonly class DataTransferObject
{
    use Buildable;
    use Makeable;
}
