<?php

namespace Mpietrucha\Laravel\Essentials\Enums\Concerns;

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

        LocaleUpdated::dispatch($locale, $previous);

        return $locale;
    }

    public function code(): string
    {
        return $this->value;
    }
}
