<?php

namespace Mpietrucha\Laravel\Package\Context\Concerns;

use Mpietrucha\Utility\Collection;

/**
 * @internal
 */
trait InteractsWithCache
{
    /**
     * @var \Mpietrucha\Utility\Collection<string, null|string>
     */
    protected static ?Collection $cache = null;

    /**
     * @return \Mpietrucha\Utility\Collection<string, null|string>
     */
    protected static function cache(): Collection
    {
        return static::$cache ??= Collection::create();
    }
}
