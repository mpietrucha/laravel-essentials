<?php

namespace Mpietrucha\Laravel\Essentials\Package\Translations;

use Mpietrucha\Laravel\Essentials\Package\Context;
use Mpietrucha\Utility\Normalizer;
use Mpietrucha\Utility\Str;
use Mpietrucha\Utility\Type;

/**
 * @phpstan-type TranslationProperties array<string, string>
 */
abstract class Translation
{
    public static function key(string $key): string
    {
        $name = Context::name();

        return Type::null($name) ? $key : Str::sprintf('%s::%s', $name, $key);
    }

    /**
     * @param  null|TranslationProperties  $properties
     */
    public static function get(string $key, ?array $properties = null): string
    {
        $key = static::key($key);

        return __($key, static::properties($properties));
    }

    /**
     * @param  null|TranslationProperties  $properties
     */
    public static function choice(string $key, int $count, ?array $properties = null): string
    {
        $key = static::key($key);

        return trans_choice($key, $count, static::properties($properties));
    }

    /**
     * @param  null|TranslationProperties  $properties
     * @return TranslationProperties
     */
    protected static function properties(?array $properties = null): array
    {
        return Normalizer::array($properties);  /** @phpstan-ignore return.type */
    }
}
