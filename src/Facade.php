<?php

namespace Mpietrucha\Laravel\Essentials;

use Illuminate\Support\Facades\Facade as IlluminateFacade;
use Mpietrucha\Support\ClassNamespace;

/**
 * @phpstan-type ClassFacade class-string<IlluminateFacade>
 */
abstract class Facade
{
    /**
     * @param  class-string  $class
     * @return ClassFacade
     */
    public static function for(string $class): string
    {
        /** @var ClassFacade */
        return ClassNamespace::join('Facade', $class);
    }
}
