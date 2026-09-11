<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Eloquent\Qualifiers;

use Mpietrucha\Laravel\Essentials\Qualifier;

class AttributeQualifier extends Qualifier
{
    public static function relationship(string $value): ?string
    {
        return static::prefix($value);
    }

    public static function attribute(string $value): string
    {
        return static::suffix($value);
    }
}
