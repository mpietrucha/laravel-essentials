<?php

namespace Mpietrucha\Laravel\Essentials\Package;

use Mpietrucha\Laravel\Essentials\Package\Context\File;
use Mpietrucha\Utility\Backtrace;
use Mpietrucha\Utility\Enumerable\Contracts\EnumerableInterface;
use Mpietrucha\Utility\Filesystem\Path;
use Mpietrucha\Utility\Str;
use Mpietrucha\Utility\Type;

abstract class Context
{
    public const bool EXTERNAL = true;

    public const bool INTERNAL = false;

    public static function directory(bool $type = self::EXTERNAL): ?string
    {
        $backtrace = Backtrace::get(DEBUG_BACKTRACE_IGNORE_ARGS, 6);

        $handler = match (true) {
            $type === self::EXTERNAL => File::external(...),
            $type === self::INTERNAL => File::internal(...)
        };

        $file = $backtrace->pipeThrough([
            fn (EnumerableInterface $backtrace) => $backtrace->map->file(),
            fn (EnumerableInterface $backtrace) => $backtrace->filter(),
            fn (EnumerableInterface $backtrace) => $backtrace->first($handler),
        ]);

        if (Type::null($file)) {
            return null;
        }

        return Str::before($file, 'src') |> Path::canonicalize(...);
    }

    public static function name(bool $type = self::EXTERNAL): ?string
    {
        $directory = static::directory($type);

        if (Type::null($directory)) {
            return null;
        }

        return Path::name($directory);
    }
}
