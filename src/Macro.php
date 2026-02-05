<?php

namespace Mpietrucha\Laravel\Package;

use Mpietrucha\Laravel\Package\Macro\Attempt;
use Mpietrucha\Laravel\Package\Macro\Exception\MacroException;
use Mpietrucha\Laravel\Package\Macro\Registry;
use Mpietrucha\Utility\Instance;

abstract class Macro
{
    public static function attach(string $destination, string $name, callable $handler): void
    {
        Registry::incompatible($destination) && MacroException::create()->throw();

        $handler = function (mixed ...$arguments) use ($name, $handler) {
            $context = isset($this) ? $this : null; /** @phpstan-ignore-line */
            $scope = static::class;

            $handler = Instance::bind($handler, $context, $scope);

            return Attempt::build($handler)->eval($name, $arguments);
        };

        $destination::macro($name, $handler);
    }
}
