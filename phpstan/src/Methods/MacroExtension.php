<?php

declare(strict_types=1);

namespace Mpietrucha\PHPStan\Methods;

use Closure;
use Mpietrucha\Laravel\Essentials\Macro;
use Mpietrucha\Laravel\Essentials\Macro\Registry;
use Mpietrucha\PHPStan\Reflection\MacroReflection;
use Mpietrucha\Utility\Normalizer;
use Mpietrucha\Utility\Type;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Type\ClosureTypeFactory;

/**
 * @internal
 */
final class MacroExtension implements MethodsClassReflectionExtension
{
    public function __construct(protected ClosureTypeFactory $factory)
    {
    }

    public function hasMethod(ClassReflection $reflection, string $method): bool
    {
        $destination = $reflection->getName();

        if (Registry::internal($destination)) {
            return false;
        }

        return $this->mixin($reflection, $method) |> Normalizer::boolean(...);
    }

    public function getMethod(ClassReflection $reflection, string $method): MacroReflection
    {
        /** @var \Closure $mixin */
        $mixin = $this->mixin($reflection, $method);

        return new MacroReflection($reflection, $method, $this->factory()->fromClosureObject($mixin));
    }

    protected function mixin(ClassReflection $reflection, string $method): ?Closure
    {
        $map = $reflection->getName() |> Macro::map()->get(...);

        if (Type::null($map)) {
            return null;
        }

        return $map->get($method);
    }

    protected function factory(): ClosureTypeFactory
    {
        return $this->factory;
    }
}
