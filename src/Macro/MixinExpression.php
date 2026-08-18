<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Macro;

use Mpietrucha\Support\ClassNamespace;
use Mpietrucha\Support\Stubs\StubRenderer;

abstract class MixinExpression
{
    public static function stub(): string
    {
        return '<?php class {{ class }} { use {{ handler }}; }; return new {{ class }};';
    }

    public static function render(string $handler): string
    {
        return StubRenderer::render(static::stub(), [
            'handler' => $handler,
            'class' => ClassNamespace::name($handler),
        ]);
    }
}
