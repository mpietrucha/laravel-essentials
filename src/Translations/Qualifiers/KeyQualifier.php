<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Translations;

use Mpietrucha\Laravel\Essentials\Qualifier;

class KeyQualifier extends Qualifier
{
    public static function group(string $value): ?string
    {
        return static::prefix($value);
    }

    public static function key(string $value): string
    {
        return static::suffix($value);
    }
}
