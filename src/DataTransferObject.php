<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use Mpietrucha\Laravel\Essentials\DataTransferObject\Concerns\Buildable;
use Mpietrucha\Laravel\Essentials\DataTransferObject\Concerns\CanBeConvertedToArray;
use Mpietrucha\Laravel\Essentials\DataTransferObject\Concerns\HasCamelCaseProperties;
use Mpietrucha\Laravel\Essentials\DataTransferObject\Concerns\Serializable;
use Mpietrucha\Support\Concerns\Makeable;

/**
 * @implements Arrayable<string, mixed>
 */
abstract readonly class DataTransferObject implements Arrayable, JsonSerializable
{
    use Buildable;
    use CanBeConvertedToArray;
    use HasCamelCaseProperties;
    use Makeable;
    use Serializable;
}
