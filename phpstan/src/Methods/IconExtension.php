<?php

declare(strict_types=1);

namespace Mpietrucha\PHPStan\Methods;

use Mpietrucha\Laravel\Essentials\Blade\Icon;
use Mpietrucha\PHPStan\Reflection\IconReflection;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;

final class IconExtension implements MethodsClassReflectionExtension
{
    public function hasMethod(ClassReflection $reflection, string $method): bool
    {
        return $reflection->getName() === Icon::class;
    }

    public function getMethod(ClassReflection $reflection, string $method): IconReflection
    {
        return new IconReflection($reflection, $method);
    }
}
