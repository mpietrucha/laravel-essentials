<?php

namespace Mpietrucha\Laravel\Essentials\Eloquent\Models\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Mpietrucha\Support\Instance;
use ReflectionMethod;
use ReflectionNamedType;

/**
 * @phpstan-require-extends Model
 */
trait DeclaresDecoratedAttributes
{
    /**
     * @param  string  $key
     */
    public function hasAttributeMutator($key): bool
    {
        $cache = Arr::get(
            /** @phpstan-ignore argument.type */
            Arr::get(static::$attributeMutatorCache, $model = Instance::namespace($this)),
            $key
        );

        if (is_bool($cache)) {
            return $cache;
        }

        /** @phpstan-ignore offsetAccess.nonOffsetAccessible */
        $set = static fn (bool $state): bool => static::$attributeMutatorCache[$model][$key] = $state;

        $method = Str::camel($key);

        if (! method_exists($this, $method)) {
            return $set(false);
        }

        $attribute = with(new ReflectionMethod($this, $method), static function (ReflectionMethod $reflectionMethod): ?string {
            $returnType = $reflectionMethod->getReturnType();

            if ($returnType === null) {
                return null;
            }

            if (! $returnType instanceof ReflectionNamedType) {
                return null;
            }

            return $returnType->getName();
        });

        if ($attribute === null) {
            return $set(false);
        }

        return is_a($attribute, Attribute::class, true) |> $set;
    }
}
