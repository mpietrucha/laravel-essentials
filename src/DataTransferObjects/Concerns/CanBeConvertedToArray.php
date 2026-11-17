<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\DataTransferObjects\Concerns;

use Illuminate\Support\Collection;
use Mpietrucha\Laravel\Essentials\DataTransferObject;
use Mpietrucha\Support\Reflection;
use Mpietrucha\Support\Str;
use ReflectionProperty;

/**
 * @phpstan-require-extends DataTransferObject
 */
trait CanBeConvertedToArray
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $properties = Reflection::make($this)->getProperties(ReflectionProperty::IS_PUBLIC);

        /** @var array<string, mixed> */
        return Collection::make($properties)->mapWithKeys(fn (ReflectionProperty $property): array => [
            $property->getName() |> Str::snake(...) => $property->getValue($this),
        ])->all();
    }
}
