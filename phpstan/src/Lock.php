<?php

namespace Mpietrucha\PHPStan;

use Illuminate\Contracts\Cache\Lock as LockInterface;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Support\Facades\Cache;
use Mpietrucha\Support\Filesystem\Temporary;

/**
 * @internal
 */
abstract class Lock
{
    public static function flush(): void
    {
        static::build()->forceRelease();
    }

    public static function acquire(int $seconds = 30): LockInterface
    {
        $lock = static::build($seconds);

        $lock->block($seconds);

        return $lock;
    }

    protected static function build(int $seconds = 0): LockInterface
    {
        $cache = Cache::build([
            'driver' => 'file',
            'path' => Temporary::path('phpstan-locks'),
        ]);

        /** @var LockProvider $lockProvider */
        $lockProvider = $cache->getStore();

        return $lockProvider->lock(base_path(), $seconds);
    }
}
