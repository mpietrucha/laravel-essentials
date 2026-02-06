<?php

namespace Mpietrucha\Laravel\Essentials;

use Closure;
use Mpietrucha\Laravel\Essentials\Macro\Attempt;
use Mpietrucha\Laravel\Essentials\Macro\Exception\MacroException;
use Mpietrucha\Laravel\Essentials\Macro\Implementation;
use Mpietrucha\Utility\Concerns\Compatible;
use Mpietrucha\Utility\Contracts\CompatibleInterface;
use Mpietrucha\Utility\Instance;

class Macro implements CompatibleInterface
{
    use Compatible;

    /**
     * @param  class-string  $destination
     */
    public static function use(string $destination, string $name, callable $handler): void
    {
        static::incompatible($destination) && MacroException::create()->throw();

        $macro = function (mixed ...$arguments) use ($name, $handler) {
            $context = isset($this) ? $this : null; /** @phpstan-ignore-line */
            $scope = static::class;

            $handler = Instance::bind($handler, $context, $scope);

            return Attempt::build($handler)->eval($name, $arguments);
        };

        $destination::macro($name, $macro);

        static::store($destination, Closure::fromCallable($handler), $name);
    }

    protected static function compatibility(string $destination): bool
    {
        return Implementation::compatible($destination);
    }
}
