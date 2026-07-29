<?php

namespace Mpietrucha\Laravel\Essentials\Blade;

use BladeUI\Icons\Factory;
use BladeUI\Icons\Svg;
use Illuminate\Support\Str;
use Mpietrucha\Support\Concerns\Makeable;
use Stringable;

readonly class Icon implements Stringable
{
    use Makeable;

    public function __construct(protected string $name)
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

    public static function factory(): Factory
    {
        return resolve(Factory::class);
    }

    public function toString(): string
    {
        $name = $this->name |> Str::kebab(...);

        $factory = static::factory() |> invade(...);

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

        return static::factory()->svg($name, $class, $attributes);
    }
}
