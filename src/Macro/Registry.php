<?php

namespace Mpietrucha\Laravel\Package\Macro;

use Mpietrucha\Utility\Arr;
use Mpietrucha\Utility\Collection;
use Mpietrucha\Utility\Concerns\Compatible;
use Mpietrucha\Utility\Contracts\CompatibleInterface;
use Mpietrucha\Utility\Enumerable\Contracts\EnumerableInterface;
use Mpietrucha\Utility\Instance;
use Mpietrucha\Utility\Type;

/**
 * @phpstan-type RegistryCollection \Mpietrucha\Utility\Collection<class-string, bool>
 * @phpstan-type TraitCollection \Mpietrucha\Utility\Enumerable\Contracts\EnumerableInterface<int, class-string>
 */
class Registry implements CompatibleInterface
{
    use Compatible;

    public const bool EXTERNAL = true;

    public const bool INTERNAL = false;

    /**
     * @var list<class-string>
     */
    protected static array $internal = [
        \Illuminate\Support\Traits\Macroable::class,
    ];

    /**
     * @var list<class-string>
     */
    protected static array $external = [
        \Spatie\Macroable\Macroable::class,
        \Filament\Support\Concerns\Macroable::class,
    ];

    /**
     * @var null|RegistryCollection
     */
    protected static ?EnumerableInterface $all = null;

    /**
     * @param  class-string  $trait
     */
    public static function use(string $trait, bool $type = self::EXTERNAL): void
    {
        static::all()->put($trait, $type);
    }

    /**
     * @return array<class-string, bool>
     */
    public static function defaults(): array
    {
        $internal = Arr::fillKeys(static::internalDefaults(), static::INTERNAL);
        $external = Arr::fillKeys(static::externalDefaults(), static::EXTERNAL);

        return $internal + $external;
    }

    /**
     * @return list<class-string>
     */
    public static function internalDefaults(): array
    {
        return static::$internal;
    }

    /**
     * @return list<class-string>
     */
    public static function externalDefaults(): array
    {
        return static::$external;
    }

    /**
     * @return RegistryCollection
     */
    public static function all(): EnumerableInterface
    {
        return static::$all ??= static::defaults() |> Collection::create(...);
    }

    /**
     * @param  class-string  $destination
     * @return TraitCollection
     */
    public static function scan(string $destination): EnumerableInterface
    {
        return Instance::traits($destination);
    }

    /**
     * @param  class-string  $destination
     */
    public static function internal(string $destination): bool
    {
        $type = static::INTERNAL;

        return static::matches($destination, $type);
    }

    /**
     * @param  class-string  $destination
     */
    public static function external(string $destination): bool
    {
        $type = static::EXTERNAL;

        return static::matches($destination, $type);
    }

    /**
     * @param  class-string  $destination
     */
    public static function matches(string $destination, ?bool $type = null): bool
    {
        $traits = static::scan($destination);

        $strict = Type::boolean($type);

        return static::all()->pipeThrough([
            fn (EnumerableInterface $all) => $all->when($strict)->whereValueStrict($type),
            fn (EnumerableInterface $all) => $all->keys(),
            fn (EnumerableInterface $all) => $all->filter(fn (string $trait) => $traits->contains($trait)),
            fn (EnumerableInterface $all) => $all->isNotEmpty(),
        ]);
    }

    /**
     * @param  class-string  $destination
     */
    protected static function compatibility(string $destination): bool
    {
        return static::matches($destination);
    }
}
