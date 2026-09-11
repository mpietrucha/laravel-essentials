<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Eloquent\Qualifiers;

use Mpietrucha\Laravel\Essentials\Qualifier;

class ColumnQualifier extends Qualifier
{
    public static function table(string $value): ?string
    {
        return static::prefix($value);
    }

    public static function column(string $value): string
    {
        return static::suffix($value);
    }
}
