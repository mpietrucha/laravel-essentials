<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\DataTransferObject\Concerns;

use Illuminate\Support\Collection;
use Mpietrucha\Laravel\Essentials\DataTransferObject;
use Mpietrucha\Support\Reflection;
use Mpietrucha\Support\Str;
use ReflectionProperty;

/**
 * @phpstan-require-extends DataTransferObject
 *
 * @phpstan-type ConvertedToArray array<string, mixed>
 */
trait CanBeConvertedToArray
{
    /**
     * @return ConvertedToArray
     */
    public function toArray(): array
    {
        return $this->convertToArray(...) |> once(...);
    }

    /**
     * @return ConvertedToArray
     */
    protected function convertToArray(): array
    {
        $properties = Reflection::make($this)->getProperties(ReflectionProperty::IS_PUBLIC);

        /** @var ConvertedToArray */
        return Collection::make($properties)->mapWithKeys(fn (ReflectionProperty $property): array => [
            $property->getName() |> Str::snake(...) => $property->getValue($this),
        ])->all();
    }
}
