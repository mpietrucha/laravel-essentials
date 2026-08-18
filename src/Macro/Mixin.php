<?php

namespace Mpietrucha\Laravel\Essentials\Macro;

use Closure;
use Illuminate\Support\Collection;
use Mpietrucha\Laravel\Essentials\Macro;
use Mpietrucha\Laravel\Essentials\Macro\Concerns\InteractsWithStorage;
use Mpietrucha\Support\ClassNamespace;
use Mpietrucha\Support\Concerns\Compatible;
use Mpietrucha\Support\Concerns\Makeable;
use Mpietrucha\Support\Exception\InvalidArgumentException;
use Mpietrucha\Support\Filesystem;
use Mpietrucha\Support\Filesystem\Temporary;
use Mpietrucha\Support\Filesystem\Touch;
use Mpietrucha\Support\Reflection;
use ReflectionMethod;

/**
 * @phpstan-import-type MacroHandlerCollection from Macro
 *
 * @phpstan-type MixinName int
 * @phpstan-type MixinTarget class-string
 * @phpstan-type MixinHandler object|class-string
 * @phpstan-type MixinHandlerCollection Collection<MixinName, MixinHandler>
 */
class Mixin
{
    use Compatible;

    /**
     * @use InteractsWithStorage<MixinTarget, MixinHandler, MixinName>
     */
    use InteractsWithStorage;

    use Makeable;

    public function __construct(protected object $handler)
    {
    }

    /**
     * @param  MixinHandler  $handler
     */
    public static function compatible(object|string $handler): bool
    {
        return is_object($handler) || trait_exists($handler);
    }

    /**
     * @param  MixinHandler  $handler
     */
    public static function build(object|string $handler): static
    {
        if (self::incompatible($handler)) {
            InvalidArgumentException::throw('Expected a trait name or object instance to be mixin handler');
        }

        if (is_object($handler)) {
            return static::make($handler);
        }

        $file = Temporary::path(ClassNamespace::toFile($handler), 'mixins');

        if (Filesystem::unexists($file)) {
            Touch::file($file);

            Filesystem::put($file, MixinExpression::render($handler));
        }

        return Filesystem::requireOnce($file) |> static::make(...);
    }

    /**
     * @param  MixinTarget  $target
     * @param  MixinHandler  $handler
     */
    public static function use(string $target, object|string $handler): void
    {
        $mixin = static::build($handler);

        $mixin->macros()->each(static function (Closure $mixin, string $name) use ($target, $handler): void {
            Macro::use($target, $name, $mixin, $handler);
        });

        static::store($target, $handler);
    }

    public function get(): object
    {
        return $this->handler;
    }

    protected function reflection(): Reflection
    {
        return $this->get() |> Reflection::make(...);
    }

    /**
     * @return MacroHandlerCollection
     */
    protected function macros(): Collection
    {
        $methods = $this->reflection()->getMethods() |> collect(...);

        $mixin = $this->get();

        return $methods->mapWithKeys(static function (ReflectionMethod $reflectionMethod) use ($mixin): array {
            if (! $reflectionMethod->isPublic()) {
                return [];
            }

            return [$reflectionMethod->getName() => $reflectionMethod->getClosure($mixin)];
        })->filter();
    }
}
