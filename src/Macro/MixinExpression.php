<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Macro;

use Illuminate\Support\Str;
use Mpietrucha\Support\ClassNamespace;

abstract class MixinExpression
{
    public static function stub(): string
    {
        return '<?php class {{ class }} { use {{ handler }}; }; return new {{ class }};';
    }

    public static function render(string $handler): string
    {
        return Str::stub(static::stub(), [
            'handler' => $handler,
            'class' => ClassNamespace::name($handler),
        ]);
    }
}
