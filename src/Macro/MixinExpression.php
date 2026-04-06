<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Macro;

use Mpietrucha\Support\ClassNamespace;

abstract class MixinExpression
{
    public static function stub(): string
    {
        return '<?php class %s { use %s; }; return new %s;';
    }

    public static function content(string $handler): string
    {
        $name = ClassNamespace::name($handler);

        return sprintf(static::stub(), $name, $handler, $name);
    }
}
