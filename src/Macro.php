<?php

namespace Mpietrucha\Laravel\Package;

use Illuminate\Support\Traits\Macroable;
use Mpietrucha\Laravel\Package\Macro\Attempt;
use Mpietrucha\Laravel\Package\Macro\Exception\MacroException;
use Mpietrucha\Utility\Concerns\Compatible;
use Mpietrucha\Utility\Contracts\CompatibleInterface;
use Mpietrucha\Utility\Instance;

abstract class Macro implements CompatibleInterface
{
    use Compatible;

    public static function attach(string $destination, string $name, callable $handler): void
    {
        static::incompatible($destination) && MacroException::create()->throw();

        $handler = function (mixed ...$arguments) use ($name, $handler) {
            $context = isset($this) ? $this : null; /** @phpstan-ignore-line */
            $scope = static::class;

            $handler = Instance::bind($handler, $context, $scope);

            return Attempt::build($handler)->eval($name, $arguments);
        };

        $destination::macro($name, $handler);
    }

    protected static function compatibility(string $destination): bool
    {
        if (Instance::unexists($destination)) {
            return false;
        }

        return Instance::traits($destination)->contains(Macroable::class);
    }
}
