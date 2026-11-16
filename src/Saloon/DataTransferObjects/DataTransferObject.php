<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Saloon\DataTransferObjects;

use Mpietrucha\Laravel\Essentials\Saloon\Concerns\DataTransferObjects\Buildable;
use Mpietrucha\Support\Concerns\Makeable;

class DataTransferObject
{
    use Buildable;
    use Makeable;
}
