<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\DataTransferObjects\Concerns;

use Mpietrucha\Laravel\Essentials\DataTransferObject;
use Mpietrucha\Support\Str;

/**
 * @phpstan-require-extends DataTransferObject
 */
trait HasCamelCaseProperties
{
    public function __get(string $property): mixed
    {
        $property = Str::camel($property);

        return $this->$property;
    }
}
