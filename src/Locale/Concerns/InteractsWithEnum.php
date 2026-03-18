<?php

namespace Mpietrucha\Laravel\Essentials\Locale\Concerns;

use Mpietrucha\Laravel\Essentials\Enums\Contracts\LocaleInterface;
use Mpietrucha\Support\Concerns\UtilizableStrings;
use Mpietrucha\Support\Exception\RuntimeException;

/**
 * @template TEnum of LocaleInterface = LocaleInterface
 *
 * @internal
 */
trait InteractsWithEnum
{
    use UtilizableStrings;

    protected static string $interface = LocaleInterface::class;

    /**
     * @return class-string<TEnum>
     */
    public static function enum(): string
    {
        $enum = static::utilize();

        if (! enum_exists($enum)) {
            RuntimeException::throw('Enum `%s` not found', $enum);
        }

        /** @var class-string<TEnum> $enum */
        $interface = static::$interface;

        if (is_a($enum, $interface, true)) {
            return $enum;
        }

        RuntimeException::throw('Enum `%s` must implement %s', $enum, $interface);
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
