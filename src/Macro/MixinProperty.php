<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Macro;

use Illuminate\Support\Arr;

abstract class MixinProperty
{
    /**
     * @var array<mixed>
     */
    protected static array $properties = [];

    public static function flush(): void
    {
        static::$properties = [];
    }

    public static function get(object $source, string $property): mixed
    {
        $identifier = static::getPropertyIdentifier($source, $property);

        return Arr::get(static::$properties, $identifier);
    }

    public static function set(object $source, string $property, mixed $value): void
    {
        $identifier = static::getPropertyIdentifier($source, $property);

        static::$properties[$identifier] = $value;
    }

    protected static function getPropertyIdentifier(object $source, string $property): string
    {
        $identifier = spl_object_hash($source);

        return sprintf('%s.%s', $identifier, $property) |> md5(...);
    }
}
