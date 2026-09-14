<?php

namespace Mpietrucha\Laravel\Essentials;

use Mpietrucha\Support\Str;

abstract class Qualifier
{
    public static function indicator(): string
    {
        return Str::dot();
    }

    public static function build(string $suffix, ?string $prefix = null): string
    {
        if ($prefix === null) {
            return $suffix;
        }

        return $prefix . static::indicator() . $suffix;
    }

    public static function hash(string $suffix, ?string $prefix = null, string $algorithm = 'md5'): string
    {
        $qualifier = static::build($suffix, $prefix);

        return hash($algorithm, $qualifier);
    }

    public static function prefix(string $value): ?string
    {
        $indicator = static::indicator();

        $prefix = Str::beforeLast($value, $indicator);

        if ($prefix === $value) {
            return null;
        }

        return Str::nullWhenEmpty($prefix);
    }

    public static function suffix(string $value): string
    {
        $indicator = static::indicator();

        return Str::afterLast($value, $indicator);
    }
}
