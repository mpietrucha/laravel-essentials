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
 * @template TInputData of array<string, mixed> = array<string, mixed>
 *
 * @implements Arrayable<string, mixed>
 */
abstract readonly class DataTransferObject implements Arrayable, JsonSerializable
{
    /**
     * @use Buildable<TInputData>
     */
    use Buildable;

    use CanBeConvertedToArray;
    use HasCamelCaseProperties;
    use Makeable;
    use Serializable;
}
