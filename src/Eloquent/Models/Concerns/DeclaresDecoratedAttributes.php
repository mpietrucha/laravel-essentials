<?php

namespace Mpietrucha\Laravel\Essentials\Eloquent\Models\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Mpietrucha\Support\Instance;
use Mpietrucha\Support\Reflection;
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

        return static::isDecoratedAttribute(new ReflectionMethod($this, $method)) |> $set(...);
    }

    /**
     * @param  object|string  $class
     * @return array<int, string>
     */
    protected static function getAttributeMarkedMutatorMethods($class)
    {
        $methods = Reflection::make($class)->getMethods() |> collect(...);

        return $methods->filter(static::isDecoratedAttribute(...))
            ->map
            ->name
            ->values()
            ->all();
    }

    protected static function isDecoratedAttribute(ReflectionMethod $reflectionMethod): bool
    {
        $returnType = $reflectionMethod->getReturnType();

        if ($returnType === null) {
            return false;
        }

        if (! $returnType instanceof ReflectionNamedType) {
            return false;
        }

        return is_a($returnType->getName(), Attribute::class, true);
    }
}
