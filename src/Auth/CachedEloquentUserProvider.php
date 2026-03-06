<?php

namespace Mpietrucha\Laravel\Essentials\Auth;

use Carbon\CarbonInterval;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Hashing\Hasher;
use Mpietrucha\Utility\Str;

use function Illuminate\Support\seconds;

class CachedEloquentUserProvider extends EloquentUserProvider
{
    public function __construct(Hasher $hasher, string $model)
    {
        parent::__construct($hasher, $model);

        $this->flush(...) |> $model::updated(...);
        $this->flush(...) |> $model::deleted(...);
    }

    public function retrieveById(mixed $identifier): ?Authenticatable
    {
        $key = $this->key($identifier);

        $ttl = $this->ttl();

        return cache()->remember($key, $ttl, fn () => parent::retrieveById($identifier));
    }

    protected function key(mixed $identifier): string
    {
        $key = config('auth.providers.users.cache.key', 'eloquent:users:%s');

        return Str::sprintf($key, $identifier);
    }

    protected function ttl(): CarbonInterval
    {
        return config('auth.providers.users.cache.ttl', 60 * 60 * 24) |> seconds(...);
    }

    protected function flush(Authenticatable $model): void
    {
        $model->getAuthIdentifier() |> $this->key(...) |> cache()->forget(...);
    }
}
