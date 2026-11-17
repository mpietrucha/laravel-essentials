<?php

namespace Mpietrucha\PHPStan;

use Illuminate\Contracts\Cache\Lock as LockAdapter;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Support\Facades\Cache;
use Mpietrucha\Support\Filesystem\Temporary;

/**
 * @internal
 */
abstract class Lock
{
    public static function make(int $seconds = 0): LockAdapter
    {
        $cache = Cache::build([
            'driver' => 'file',
            'path' => Temporary::path('phpstan-locks'),
        ]);

        /** @var LockProvider $provider */
        $provider = $cache->getStore();

        return $provider->lock(base_path(), $seconds);
    }

    public static function flush(): void
    {
        $lock = static::make();

        $lock->forceRelease();
    }

    public static function acquire(int $seconds = 300): LockAdapter
    {
        $lock = static::make($seconds);

        $lock->block($seconds);

        return $lock;
    }
}
