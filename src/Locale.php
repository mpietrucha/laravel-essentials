<?php

namespace Mpietrucha\Laravel\Essentials;

use Mpietrucha\Laravel\Essentials\Locale\Concerns\InteractsWithEnum;
use Mpietrucha\Laravel\Essentials\Locale\Contracts\InteractsWithEnumInterface;

abstract class Locale implements InteractsWithEnumInterface
{
    use InteractsWithEnum;

    public static function get(): string
    {
        return app()->getLocale();
    }

    public static function set(string $locale): void
    {
        app()->setLocale($locale);
    }

    protected static function hydrate(): string
    {
        /** @phpstan-ignore class.notFound */
        return \App\Enums\Locale::class;
    }
}
