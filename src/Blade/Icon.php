<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Blade;

use BladeUI\Icons\Svg;
use Illuminate\Contracts\Support\Htmlable;
use Mpietrucha\Support\Concerns\Makeable;
use Mpietrucha\Support\Str;
use Stringable;

readonly class Icon implements Htmlable, Stringable
{
    use Makeable;

    public function __construct(protected ?string $name = null)
    {
    }

    /**
     * @param  array<mixed>  $arguments
     */
    public static function __callStatic(string $method, array $arguments): static
    {
        return static::make($method);
    }

    /**
     * @param  array<mixed>  $arguments
     */
    public function __call(string $method, array $arguments): static
    {
        return $this->toString($method) |> static::make(...);
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(?string $name = null): string
    {
        if ($this->name === null) {
            return $name ?? Str::none();
        }

        if ($name === null) {
            return $this->name ?? Str::none();
        }

        return sprintf('%s-%s', $this->name, $name);
    }

    public function toHtml(): string
    {
        return $this->render();
    }

    /**
     * @param  array<mixed>  $attributes
     */
    public function svg(string $class = '', array $attributes = [], ?string $name = null): Svg
    {
        $name = $this->toString($name);

        return svg($name, $class, $attributes);
    }

    /**
     * @param  array<mixed>  $attributes
     */
    public function render(string $class = '', array $attributes = [], ?string $name = null): string
    {
        return $this->svg($class, $attributes, $name)->toHtml();
    }
}
