<?php

namespace Mpietrucha\Laravel\Package\Mixin;

use Mpietrucha\Utility\Str;

abstract class Expression
{
    public static function trait(string $trait): object
    {
        $expression = Str::sprintf('return new class { use %s; };', $trait);

        return eval($expression);
    }
}
