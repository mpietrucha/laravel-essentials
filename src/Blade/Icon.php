<?php

namespace Mpietrucha\Laravel\Essentials\Blade;

use BladeUI\Icons\Svg;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;
use Mpietrucha\Support\Concerns\Makeable;
use Stringable;

readonly class Icon implements Htmlable, Stringable
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

    public function toString(): string
    {
        return $this->name |> Str::kebab(...);
    }

    public function toHtml(): string
    {
        return $this->render();
    }

    /**
     * @param  array<mixed>  $attributes
     */
    public function svg(string $class = '', array $attributes = []): Svg
    {
        $name = $this->toString();

        return svg($name, $class, $attributes);
    }

    /**
     * @param  array<mixed>  $attributes
     */
    public function render(string $class = '', array $attributes = []): string
    {
        return $this->svg($class, $attributes)->toHtml();
    }
}
