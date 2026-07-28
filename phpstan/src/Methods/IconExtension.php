<?php

declare(strict_types=1);

namespace Mpietrucha\PHPStan\Methods;

use BladeUI\Icons\Factory;
use Mpietrucha\Laravel\Essentials\Blade\Icon;
use Mpietrucha\PHPStan\Reflection\IconReflection;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use Throwable;

final class IconExtension implements MethodsClassReflectionExtension
{
    public function hasMethod(ClassReflection $reflection, string $method): bool
    {
        if (! $reflection->is(Icon::class)) {
            return false;
        }

        $factory = resolve(Factory::class);

        try {
            Icon::make($method)->svg();

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    public function getMethod(ClassReflection $reflection, string $method): IconReflection
    {
        return new IconReflection($reflection, $method);
    }
}
