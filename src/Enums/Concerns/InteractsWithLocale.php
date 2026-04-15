<?php

namespace Mpietrucha\Laravel\Essentials\Enums\Concerns;

use Closure;
use Mpietrucha\Laravel\Essentials\Enums\Contracts\LocaleInterface;
use Mpietrucha\Laravel\Essentials\Events\LocaleUpdated;
use Mpietrucha\Support\Enums\Concerns\InteractsWithEnum;

/**
 * @phpstan-require-implements LocaleInterface
 */
trait InteractsWithLocale
{
    use InteractsWithEnum;

    public static function get(): static
    {
        return app()->getLocale() |> static::from(...);
    }

    public static function set(mixed $locale): static
    {
        $locale = static::build($locale);

        $previous = static::get();

        $locale->code() |> app()->setLocale(...);

        event(new LocaleUpdated($locale, $previous));

        return $locale;
    }

    public static function with(mixed $locale, Closure $callback): mixed
    {
        $original = static::get();

        try {
            static::set($locale);

            return $callback();
        } finally {
            $original->activate();
        }
    }

    public function code(): string
    {
        return $this->value;
    }

    public function activate(): void
    {
        static::set($this);
    }
}
