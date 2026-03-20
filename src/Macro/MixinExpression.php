<?php

namespace Mpietrucha\Laravel\Essentials\Macro;

use Mpietrucha\Support\Instance\Path;

abstract class MixinExpression
{
    public static function stub(): string
    {
        return '<?php class %s { use %s; }; return new %s;';
    }

    public static function content(string $handler): string
    {
        $name = Path::name($handler);

        return sprintf(static::stub(), $name, $handler, $name);
    }
}
