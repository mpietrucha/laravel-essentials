<?php

namespace Mpietrucha\Laravel\Essentials\Macro\Concerns;

use Illuminate\Support\Collection;
use Mpietrucha\Support\Arr;
use Mpietrucha\Support\Context;

/**
 * @template TGroup of string
 * @template TValue of mixed
 * @template TKey of array-key = int
 */
trait InteractsWithStorage
{
    /**
     * @var null|Collection<TGroup, Collection<TKey, TValue>>
     */
    protected static ?Collection $storage = null;

    /**
     * @return Collection<TGroup, Collection<TKey, TValue>>
     */
    public static function storage(): Collection
    {
        return static::$storage ??= Collection::empty();
    }

    /**
     * @param  TGroup  $group
     * @param  TValue  $value
     * @param  null|TKey  $key
     */
    protected static function store(string $group, mixed $value, null|int|string $key = null): void
    {
        if (Context::web()) {
            return;
        }

        /** @var array{TValue, TKey} $arguments */
        $arguments = [$value, $key] |> Arr::whereNotNull(...);

        $bucket = Collection::make(...);

        static::storage()->getOrPut($group, $bucket)->prepend(...$arguments);
    }
}
