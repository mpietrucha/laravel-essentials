<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials;

use Mpietrucha\Support\ClassNamespace;

abstract class Facade
{
    public static function prefix(): string
    {
        return 'Facades';
    }

    /**
     * @param  class-string  $class
     */
    public static function for(string $class): string
    {
        return ClassNamespace::join(static::prefix(), $class);
    }
}
