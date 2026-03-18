<?php

declare(strict_types=1);

namespace Mpietrucha\PHPStan\Methods;

use Closure;
use Mpietrucha\Laravel\Essentials\Macro;
use Mpietrucha\PHPStan\Reflection\MacroReflection;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Type\ClosureTypeFactory;

/**
 * @internal
 */
final class MacroExtension implements MethodsClassReflectionExtension
{
    protected ?Closure $mixin = null;

    public function __construct(protected ClosureTypeFactory $closureTypeFactory)
    {
    }

    public function hasMethod(ClassReflection $reflection, string $method): bool
    {
        return (bool) $this->mixin = $this->mixin($reflection, $method);
    }

    public function getMethod(ClassReflection $reflection, string $method): MacroReflection
    {
        /** @var Closure $mixin */
        $mixin = $this->mixin;

        return new MacroReflection($reflection, $method, $this->closureTypeFactory->fromClosureObject($mixin));
    }

    protected function mixin(ClassReflection $reflection, string $method): ?Closure
    {
        while ($reflection) {
            $map = $reflection->getName() |> Macro::storage()->get(...);

            $reflection = $reflection->getParentClass();

            if ($map === null) {
                continue;
            }

            $mixin = $map->get($method);

            if ($mixin === null) {
                continue;
            }

            return $mixin;
        }

        return null;
    }
}
