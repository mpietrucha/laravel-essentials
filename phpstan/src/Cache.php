<?php

namespace Mpietrucha\PHPStan;

use Illuminate\Support\Collection;
use Mpietrucha\Support\Concerns\UtilizableStrings;
use Mpietrucha\Support\Filesystem;
use Mpietrucha\Support\Filesystem\Path;
use Mpietrucha\Support\Filesystem\Temporary;
use Throwable;

/**
 * @internal
 *
 * @phpstan-type StorageCollection Collection<string, string>
 */
abstract class Cache
{
    use UtilizableStrings;

    /**
     * @var null|StorageCollection
     */
    protected static ?Collection $storage = null;

    public static function flush(): void
    {
        static::$storage = null;

        static::file() |> Filesystem::delete(...);
    }

    public static function dirty(string $value): bool
    {
        /** @var null|string $indicator */
        $indicator = static::utilize();

        if ($indicator === null) {
            return true;
        }

        if (static::storage()->doesntContain($indicator)) {
            static::flush();

            static::synchronize($indicator);
        }

        if (static::storage()->doesntContain($value)) {
            static::synchronize($value);

            return true;
        }

        return false;
    }

    protected static function synchronize(string $value): void
    {
        static::storage()->push($value);

        $file = static::file();

        Filesystem::put($file, static::storage()->toJson());
    }

    protected static function file(): string
    {
        return Temporary::file('phpstan-actions-cache.json');
    }

    /**
     * @return StorageCollection
     */
    protected static function storage(): Collection
    {
        return static::$storage ??= static::file() |> Filesystem::json(...) |> collect(...);
    }

    protected static function hydrate(): ?string
    {
        try {
            return Path::cwd('composer.lock') |> Filesystem::hash(...) ?: null;
        } catch (Throwable) {
            return null;
        }
    }
}
