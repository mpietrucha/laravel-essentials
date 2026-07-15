<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Blade;

use BladeUI\Icons\Svg;
use Mpietrucha\Support\Concerns\Makeable;

readonly class Icon
{
    use Makeable;

    public function __construct(protected ?string $prefix = null)
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
    public function __call(string $method, array $arguments): string
    {
        return $this->name($method);
    }

    public function name(string $icon): string
    {
        $prefix = $this->prefix;

        if ($prefix === null) {
            return $icon;
        }

        return sprintf('%s-%s', $prefix, $icon);
    }

    /**
     * @param  array<mixed>  $attributes
     */
    public function svg(string $icon, string $class = '', array $attributes = []): Svg
    {
        $icon = $this->name($icon);

        return svg($icon, $class, $attributes);
    }

    /**
     * @param  array<mixed>  $attributes
     */
    public function render(string $icon, string $class = '', array $attributes = []): string
    {
        return $this->svg($icon, $class, $attributes)->toHtml();
    }
}
