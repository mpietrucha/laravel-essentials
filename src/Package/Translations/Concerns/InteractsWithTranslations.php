<?php

namespace Mpietrucha\Laravel\Essentials\Package\Translations\Concerns;

use Mpietrucha\Laravel\Essentials\Package\Translations\Translation;

/**
 * @phpstan-import-type TranslationProperties from \Mpietrucha\Laravel\Essentials\Package\Translations\Translation
 */
trait InteractsWithTranslations
{
    /**
     * @param  null|TranslationProperties  $properties
     */
    public static function __(string $key, ?array $properties = null): string
    {
        return static::t($key, $properties);
    }

    /**
     * @param  null|TranslationProperties  $properties
     */
    public static function t(string $key, ?array $properties = null): string
    {
        return Translation::get($key, $properties);
    }

    /**
     * @param  null|TranslationProperties  $properties
     */
    public static function tc(string $key, int $count, ?array $properties = null): string
    {
        return Translation::choice($key, $count, $properties);
    }
}
