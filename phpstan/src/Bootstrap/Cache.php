<?php

namespace Mpietrucha\PHPStan\Bootstrap;

use Mpietrucha\Utility\Collection;
use Mpietrucha\Utility\Enumerable\Contracts\EnumerableInterface;
use Mpietrucha\Utility\Filesystem;
use Mpietrucha\Utility\Filesystem\Temporary;
use Mpietrucha\Utility\Type;
use Mpietrucha\Utility\Utilizer\Concerns\Utilizable;
use Mpietrucha\Utility\Utilizer\Contracts\UtilizableInterface;
use Mpietrucha\Utility\Value;

/**
 * @internal
 *
 * @phpstan-type StorageCollection \Mpietrucha\Utility\Collection<string, string>
 */
abstract class Cache implements UtilizableInterface
{
    use Utilizable\Strings;

    /**
     * @var null|StorageCollection
     */
    protected static ?EnumerableInterface $storage = null;

    public static function flush(): void
    {
        static::file() |> Filesystem::delete(...);
    }

    public static function dirty(string $value): bool
    {
        $indicator = static::utilize();

        if (Type::null($indicator)) {
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
    protected static function storage(): EnumerableInterface
    {
        return static::$storage ??= static::file() |> Filesystem::json(...) |> Collection::create(...);
    }

    protected static function hydrate(): ?string
    {
        $hash = Filesystem::hash(...);

        return base_path('composer.lock') |> Value::attempt($hash)->value(...);
    }
}
