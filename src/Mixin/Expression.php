<?php

namespace Mpietrucha\Laravel\Essentials\Mixin;

use Mpietrucha\Utility\Instance\Path;
use Mpietrucha\Utility\Str;

abstract class Expression
{
    public static function generate(string $trait): string
    {
        $name = static::name($trait);

        return Str::sprintf('<?php class %s { use %s; }; return new %s;', $name, $trait, $name);
    }

    protected static function name(string $trait): string
    {
        $delimiter = Path::delimiter();

        return Str::of($trait)->remove($delimiter)->finish('TraitMixinInstance');
    }
}
