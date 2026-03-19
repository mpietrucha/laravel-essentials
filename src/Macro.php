<?php

namespace Mpietrucha\Laravel\Essentials;

use Closure;
use Filament\Support\Concerns\Macroable as FilamentMacroable;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable as IlluminateMacroable;
use Mpietrucha\Laravel\Essentials\Macro\Concerns\InteractsWithStorage;
use Mpietrucha\Laravel\Essentials\Macro\Mixin;
use Mpietrucha\Support\Concerns\Compatible;
use Mpietrucha\Support\Exception\InvalidArgumentException;
use Mpietrucha\Support\Instance;
use Spatie\Macroable\Macroable as SpatieMacroable;

/**
 * @phpstan-import-type MixinTarget from Mixin
 * @phpstan-import-type MixinHandler from Mixin
 *
 * @phpstan-type MacroName string
 * @phpstan-type MacroTarget class-string
 * @phpstan-type MacroHandler Closure
 * @phpstan-type MacroHandlerCollection Collection<MacroName, MacroHandler>
 */
class Macro
{
    use Compatible;

    /**
     * @use InteractsWithStorage<MacroTarget, MacroHandler, MacroName>
     */
    use InteractsWithStorage;

    public static function compatible(string $target): bool
    {
        $traits = Instance::traits($target);

        return $traits->hasAny([
            IlluminateMacroable::class, SpatieMacroable::class, FilamentMacroable::class,
        ]);
    }

    /**
     * @param  MacroTarget  $target
     * @param  MacroName  $name
     * @param  MacroHandler  $handler
     * @param  null|MixinHandler  $mixin
     */
    public static function use(string $target, string $name, Closure $handler, null|object|string $mixin = null): void
    {
        if (self::incompatible($target)) {
            InvalidArgumentException::throw('Macro destination does not use any of the supported Macroable implementations');
        }

        $macro = function (mixed ...$arguments) use ($handler, $mixin) {
            $context = $this ?? null; /** @phpstan-ignore nullCoalesce.variable */
            $scope = static::class;

            $handler = Instance::bind($handler, $context, $scope, $mixin);

            return $handler(...$arguments);
        };

        $target::macro($name, $macro);

        static::store($target, $handler, $name);
    }

    /**
     * @param  MixinTarget  $target
     * @param  MixinHandler  $handler
     */
    public static function mixin(string $target, object|string $handler): void
    {
        Mixin::use($target, $handler);
    }
}
