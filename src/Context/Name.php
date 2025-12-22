<?php

namespace Mpietrucha\Laravel\Package\Context;

use Mpietrucha\Laravel\Package\Context\Concerns\InteractsWithCache;
use Mpietrucha\Utility\Filesystem;
use Mpietrucha\Utility\Str;
use Mpietrucha\Utility\Type;
use Mpietrucha\Utility\Utilizer\Concerns\Utilizable;
use Mpietrucha\Utility\Utilizer\Contracts\UtilizableInterface;

abstract class Name implements UtilizableInterface
{
    use InteractsWithCache, Utilizable\Strings;

    public static function get(): ?string
    {
        $provider = Provider::get();

        if (Type::null($provider)) {
            return null;
        }

        if ($name = static::cache()->get($provider)) {
            return $name;
        }

        static::cache()->put($provider, $name = static::build($provider));

        return $name;
    }

    protected static function build(string $provider): ?string
    {
        $expression = static::utilize();

        return Str::match($expression, Filesystem::get($provider)) |> Str::nullWhenEmpty(...);
    }

    protected static function hydrate(): string
    {
        return "/name\(['\"]([^'\"]+)['\"]\)/";
    }
}
