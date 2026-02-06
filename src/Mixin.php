<?php

namespace Mpietrucha\Laravel\Essentials;

use Mpietrucha\Laravel\Essentials\Mixin\Exception\MixinException;
use Mpietrucha\Laravel\Essentials\Mixin\Expression;
use Mpietrucha\Utility\Collection;
use Mpietrucha\Utility\Concerns\Compatible;
use Mpietrucha\Utility\Concerns\Creatable;
use Mpietrucha\Utility\Contracts\CompatibleInterface;
use Mpietrucha\Utility\Contracts\CreatableInterface;
use Mpietrucha\Utility\Enumerable\Contracts\EnumerableInterface;
use Mpietrucha\Utility\Filesystem;
use Mpietrucha\Utility\Filesystem\Temporary;
use Mpietrucha\Utility\Instance;
use Mpietrucha\Utility\Reflection;
use Mpietrucha\Utility\Reflection\Contracts\ReflectionInterface;
use Mpietrucha\Utility\Type;
use Mpietrucha\Utility\Value;

class Mixin implements CompatibleInterface, CreatableInterface
{
    use Compatible, Creatable;

    public function __construct(protected object $mixin)
    {
    }

    /**
     * @param  object|class-string  $mixin
     */
    public static function build(object|string $mixin): static
    {
        static::incompatible($mixin) && MixinException::create()->throw();

        if (Type::object($mixin)) {
            return static::create($mixin);
        }

        $file = Temporary::get($mixin);

        if (Filesystem::unexists($file)) {
            Filesystem::put($file, Expression::generate($mixin));
        }

        return Filesystem::requireOnce($file) |> static::create(...);
    }

    /**
     * @param  class-string  $destination
     * @param  object|class-string  $mixin
     */
    public static function use(string $destination, object|string $mixin): void
    {
        $mixin = static::build($mixin);

        $handler = Macro::use(...);

        Value::pipe($destination, $handler) |> $mixin->macros()->eachKeys(...);

        static::store($destination, $mixin->get());
    }

    public function get(): object
    {
        return $this->mixin;
    }

    protected function reflection(): ReflectionInterface
    {
        return $this->get() |> Reflection::create(...);
    }

    /**
     * @return \Mpietrucha\Utility\Enumerable\Contracts\EnumerableInterface<int, callable>
     */
    protected function macros(): EnumerableInterface
    {
        $methods = $this->reflection()->getMethods();

        return Collection::create($methods)->pipeThrough([
            fn (EnumerableInterface $methods) => $methods->filter->isPublic(),
            fn (EnumerableInterface $methods) => $methods->keyBy->getName(),
            fn (EnumerableInterface $methods) => $this->get() |> $methods->map->getClosure(...),
        ]);
    }

    protected static function compatibility(object|string $mixin): bool
    {
        if (Type::object($mixin)) {
            return true;
        }

        return Instance::trait($mixin);
    }
}
