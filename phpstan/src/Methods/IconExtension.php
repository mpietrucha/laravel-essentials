<?php

declare(strict_types=1);

namespace Mpietrucha\PHPStan\Methods;

use Mpietrucha\Laravel\Essentials\Blade\Icon;
use Mpietrucha\PHPStan\Reflection\IconReflection;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use Throwable;

final class IconExtension implements MethodsClassReflectionExtension
{
    public function hasMethod(ClassReflection $classReflection, string $method): bool
    {
        if (! $classReflection->is(Icon::class)) {
            return false;
        }

        try {
            Icon::make($method)->svg();

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    public function getMethod(ClassReflection $classReflection, string $method): IconReflection
    {
        return new IconReflection($classReflection, $method);
    }
}
