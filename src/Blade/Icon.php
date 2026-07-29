<?php

namespace Mpietrucha\Laravel\Essentials\Blade;

use BladeUI\Icons\Factory;
use BladeUI\Icons\Svg;
use Illuminate\Support\Str;
use Mpietrucha\Support\Concerns\Makeable;
use Stringable;

class Icon implements Stringable
{
    use Makeable;

    protected ?Factory $factory = null;

    public function __construct(protected readonly string $name)
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

    public function factory(): Factory
    {
        return $this->factory ??= resolve(Factory::class);
    }

    public function toString(): string
    {
        $name = $this->name |> Str::kebab(...);

        $factory = $this->factory() |> invade(...);

        /** @phpstan-ignore-next-line */
        $factory->contents(...$factory->splitSetAndName($name));

        return $name;
    }

    /**
     * @param  array<mixed>  $attributes
     */
    public function svg(string $class = '', array $attributes = []): Svg
    {
        $name = $this->toString();

        return $this->factory()->svg($name, $class, $attributes);
    }
}
