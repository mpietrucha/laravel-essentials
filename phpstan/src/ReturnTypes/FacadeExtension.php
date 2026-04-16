<?php

namespace Mpietrucha\PHPStan\ReturnTypes;

use Illuminate\Support\Arr;
use Mpietrucha\Laravel\Essentials\Facade;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\Generic\GenericClassStringType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

final class FacadeExtension implements DynamicStaticMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return Facade::class;
    }

    public function isStaticMethodSupported(MethodReflection $method): bool
    {
        return $method->getName() === 'for';
    }

    public function getTypeFromStaticMethodCall(MethodReflection $method, StaticCall $call, Scope $scope): Type
    {
        $value = data_get($call->getArgs(), '{first}.value');

        if (! $value instanceof Expr) {
            return new StringType;
        }

        $classNames = $scope->getType($value)->getClassStringObjectType()->getObjectClassNames();

        if ($classNames === []) {
            return new StringType;
        }

        /** @var array<GenericClassStringType> $types */
        $types = Arr::map($classNames, static function (string $class): GenericClassStringType {
            /** @var class-string $class */
            $facade = Facade::for($class);

            return new GenericClassStringType(new ObjectType($facade));
        });

        return TypeCombinator::union(...$types);
    }
}
