<?php

namespace Mpietrucha\PHPStan;

use Illuminate\Contracts\Cache\Lock as Adapter;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Support\Facades\Cache;
use Mpietrucha\Support\Filesystem\Temporary;

/**
 * @internal
 */
abstract class Lock
{
    public static function acquire(int $seconds = 300): Adapter
    {
        $provider = static::getLockProvider();

        $lock = $provider->lock(base_path(), $seconds);

        $lock->block($seconds);

        return $lock;
    }

    protected static function getLockProvider(): LockProvider
    {
        $cache = Cache::build([
            'driver' => 'file',
            'path' => Temporary::path('phpstan-locks'),
        ]);

        /** @var LockProvider */
        return $cache->getStore();
    }
}
