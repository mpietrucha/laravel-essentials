<?php

declare(strict_types=1);

namespace Mpietrucha\PHPStan\Methods;

use Closure;
use Mpietrucha\Laravel\Essentials\Macro;
use Mpietrucha\PHPStan\Reflection\MacroReflection;
use Mpietrucha\Support\Reflection\ReflectionClosure;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\ClosureTypeFactory;

/**
 * @internal
 */
final class MacroExtension implements MethodsClassReflectionExtension
{
    private ?Closure $mixin = null;

    public function __construct(private readonly ClosureTypeFactory $closureTypeFactory, private readonly ReflectionProvider $reflectionProvider)
    {
    }

    public function hasMethod(ClassReflection $classReflection, string $method): bool
    {
        return (bool) $this->mixin = $this->mixin($classReflection, $method);
    }

    public function getMethod(ClassReflection $classReflection, string $method): MacroReflection
    {
        /** @var Closure $mixin */
        $mixin = $this->mixin;

        $scope = ReflectionClosure::make($mixin)->getClosureScopeClass()?->getName();

        if ($scope) {
            $methodReflection = $this->reflectionProvider->getClass($scope)->getNativeMethod($method);

            return new MacroReflection($classReflection, $method, $methodReflection->getVariants());
        }

        $closureType = $this->closureTypeFactory->fromClosureObject($mixin);

        return new MacroReflection($classReflection, $method, MacroReflection::buildVariantsFromClosureType($closureType));
    }

    private function mixin(ClassReflection $classReflection, string $method): ?Closure
    {
        while ($classReflection instanceof ClassReflection) {
            $map = $classReflection->getName() |> Macro::storage()->get(...);

            $classReflection = $classReflection->getParentClass();

            $mixin = $map?->get($method);

            if ($mixin === null) {
                continue;
            }

            return $mixin;
        }

        return null;
    }
}
