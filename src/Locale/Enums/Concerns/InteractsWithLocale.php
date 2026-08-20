<?php

namespace Mpietrucha\Laravel\Essentials\Locale\Enums\Concerns;

use Closure;
use Mpietrucha\Laravel\Essentials\Locale\Enums\Contracts\LocaleInterface;
use Mpietrucha\Laravel\Essentials\Locale\Events\LocaleUpdated;
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
        $currentLocale = static::get();

        try {
            static::set($locale);

            return $callback();
        } finally {
            $currentLocale->apply();
        }
    }

    public function code(): string
    {
        return $this->value;
    }

    public function apply(): void
    {
        static::set($this);
    }
}
