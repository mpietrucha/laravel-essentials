<?php

namespace Mpietrucha\Laravel\Essentials\Locale\Concerns;

use Mpietrucha\Laravel\Essentials\Enums\Contracts\LocaleInterface;
use Mpietrucha\Support\Concerns\UtilizableStrings;
use Mpietrucha\Support\Enum;

/**
 * @template TEnum of LocaleInterface = LocaleInterface
 *
 * @internal
 */
trait InteractsWithEnum
{
    use UtilizableStrings;

    /**
     * @return class-string<TEnum>
     */
    public static function enum(): string
    {
        $interface = static::$interface;

        /** @var class-string<TEnum> */
        return Enum::unit(static::utilize(), $interface);
    }

    /**
     * @return TEnum
     */
    public static function get(): LocaleInterface
    {
        return static::enum()::get();
    }

    /**
     * @return TEnum
     */
    public static function set(string $value): LocaleInterface
    {
        return static::enum()::set($value);
    }

    protected static function hydrate(): string
    {
        return static::$enum;
    }
}
