<?php

namespace Mpietrucha\Laravel\Essentials;

class Locale
{
    public static function get(): string
    {
        return app()->getLocale();
    }

    public static function set(string $locale): void
    {
        app()->setLocale($locale);
    }
}
