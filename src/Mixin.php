<?php

namespace Mpietrucha\Laravel\Package;

use Mpietrucha\Laravel\Package\Mixin\Exception\MixinException;
use Mpietrucha\Laravel\Package\Mixin\Expression;
use Mpietrucha\Utility\Collection;
use Mpietrucha\Utility\Concerns\Compatible;
use Mpietrucha\Utility\Concerns\Creatable;
use Mpietrucha\Utility\Contracts\CompatibleInterface;
use Mpietrucha\Utility\Contracts\CreatableInterface;
use Mpietrucha\Utility\Enumerable\Contracts\EnumerableInterface;
use Mpietrucha\Utility\Instance;
use Mpietrucha\Utility\Reflection;
use Mpietrucha\Utility\Reflection\Contracts\ReflectionInterface;
use Mpietrucha\Utility\Type;
use Mpietrucha\Utility\Value;

class Mixin implements CompatibleInterface, CreatableInterface
{
    use Compatible, Creatable;

    protected ?ReflectionInterface $reflection = null;

    public function __construct(protected object $instance)
    {
    }

    public static function build(object|string $instance): static
    {
        static::incompatible($instance) && MixinException::create()->throw();

        if (Type::object($instance)) {
            return static::create($instance);
        }

        return Expression::trait($instance) |> static::create(...);
    }

    public static function attach(string $destination, object|string $instance): void
    {
        $mixin = static::build($instance);

        $handler = Macro::attach(...);

        Value::pipe($destination, $handler) |> $mixin->macros()->eachKeys(...);
    }

    public function instance(): object
    {
        return $this->instance;
    }

    public function reflection(): ReflectionInterface
    {
        return $this->reflection ??= $this->instance() |> Reflection::create(...);
    }

    /**
     * @return \Mpietrucha\Utility\Enumerable\Contracts\EnumerableInterface<string, callable>
     */
    public function macros(): EnumerableInterface
    {
        $methods = $this->reflection()->getMethods();

        return Collection::create($methods)->pipeThrough([
            fn (EnumerableInterface $methods) => $methods->filter->isPublic(),
            fn (EnumerableInterface $methods) => $methods->keyBy->getName(),
            fn (EnumerableInterface $methods) => $this->instance() |> $methods->map->getClosure(...),
        ]);
    }

    protected static function compatibility(object|string $instance): bool
    {
        if (Type::object($instance)) {
            return true;
        }

        return Instance::trait($instance);
    }
}
