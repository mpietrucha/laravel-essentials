<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Macro\Concerns;

use Mpietrucha\Laravel\Essentials\Macro\MixinProperty;

trait InteractsWithMixinProperty
{
    public static function getMixinProperty(object $source, ?string $property = null): mixed
    {
        $property = static::getMixinPropertyIdentifier($property);

        return MixinProperty::get($source, $property);
    }

    public static function setMixinProperty(object $source, mixed $value, ?string $property = null): void
    {
        $property = static::getMixinPropertyIdentifier($property);

        MixinProperty::set($source, $property, $value);
    }

    protected static function getMixinPropertyIdentifier(?string $property = null): string
    {
        $identifier = static::class;

        if ($property === null) {
            return $identifier;
        }

        return sprintf('%s.%s', $identifier, $property) |> md5(...);
    }
}
