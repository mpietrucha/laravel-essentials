<?php

namespace Mpietrucha\Laravel\Essentials\Auth;

use Carbon\CarbonInterval;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Hashing\Hasher;
use Mpietrucha\Support\Exception\InvalidArgumentException;

use function Illuminate\Support\seconds;

class CachedEloquentUserProvider extends EloquentUserProvider
{
    public function __construct(Hasher $hasher, string $model)
    {
        parent::__construct($hasher, $model);

        $this->flushCache(...) |> $model::updated(...);
        $this->flushCache(...) |> $model::deleted(...);
    }

    #[\Override]
    public function retrieveById(mixed $identifier): ?Authenticatable
    {
        $cacheKey = $this->getCacheKey($identifier);

        $cacheTTL = $this->getCacheTTL();

        return cache()->remember($cacheKey, $cacheTTL, fn () => parent::retrieveById($identifier));
    }

    protected function getCacheKey(mixed $identifier): string
    {
        if (! is_scalar($identifier)) {
            InvalidArgumentException::throw('Auth identifier must be a scalar value');
        }

        return sprintf(config()->string('auth.providers.users.cache.key', 'eloquent:users:%s'), $identifier);
    }

    protected function getCacheTTL(): CarbonInterval
    {
        return config()->integer('auth.providers.users.cache.ttl', 60 * 60 * 24) |> seconds(...);
    }

    protected function flushCache(Authenticatable $authenticatable): void
    {
        $authenticatable->getAuthIdentifier() |> $this->getCacheKey(...) |> cache()->forget(...);
    }
}
