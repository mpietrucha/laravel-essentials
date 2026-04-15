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

    public static function set(string $locale): static
    {
        $locale = static::from($locale);

        $previous = static::get();

        $locale->code() |> app()->setLocale(...);

        event(new LocaleUpdated($locale, $previous));

        return $locale;
    }

    public static function with(string $locale, Closure $callback): mixed
    {
        $original = static::get();

        try {
            static::set($locale);

            return $callback();
        } finally {
            $original->code() |> static::set(...);
        }
    }

    public function code(): string
    {
        return $this->value;
    }
}
