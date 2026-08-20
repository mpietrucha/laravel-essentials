<?php

namespace Mpietrucha\Laravel\Essentials\Blade;

use BladeUI\Icons\Factory as BladeUIFactory;
use BladeUI\Icons\Svg;
use Illuminate\Support\Str;
use Mpietrucha\Support\Concerns\Makeable;
use Stringable;

class Icon implements Stringable
{
    use Makeable;

    public function __construct(protected readonly string $name, protected ?BladeUIFactory $adapter = null)
    {
    }

    /**
     * @param  array<mixed>  $arguments
     */
    public static function __callStatic(string $method, array $arguments): static
    {
        return static::make($method);
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function adapter(): BladeUIFactory
    {
        if ($this->adapter instanceof BladeUIFactory) {
            return $this->adapter;
        }

        return $this->adapter = resolve(BladeUIFactory::class);
    }

    public function toString(): string
    {
        $name = $this->name |> Str::kebab(...);

        $bladeUIFactory = $this->adapter() |> invade(...);

        /** @phpstan-ignore-next-line */
        $bladeUIFactory->contents(...$bladeUIFactory->splitSetAndName($name));

        return $name;
    }

    /**
     * @param  array<mixed>  $attributes
     */
    public function svg(string $class = '', array $attributes = []): Svg
    {
        $name = $this->toString();

        return $this->adapter()->svg($name, $class, $attributes);
    }
}
