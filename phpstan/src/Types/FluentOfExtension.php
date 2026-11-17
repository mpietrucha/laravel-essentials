<?php

namespace Mpietrucha\PHPStan\Types;

use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use Mpietrucha\Support\Arr;
use PHPStan\Analyser\NameScope;
use PHPStan\PhpDoc\TypeNodeResolver;
use PHPStan\PhpDoc\TypeNodeResolverAwareExtension;
use PHPStan\PhpDoc\TypeNodeResolverExtension;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectShapeType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

final class FluentOfExtension implements TypeNodeResolverAwareExtension, TypeNodeResolverExtension
{
    private TypeNodeResolver $typeNodeResolver;

    public function setTypeNodeResolver(TypeNodeResolver $typeNodeResolver): void
    {
        $this->typeNodeResolver = $typeNodeResolver;
    }

    public function resolve(TypeNode $typeNode, NameScope $nameScope): ?Type
    {
        if (! $this->isFluentOfNodeType($typeNode)) {
            return null;
        }

        $genericType = $typeNode->genericTypes |> Arr::first(...);

        if (! $genericType instanceof TypeNode) {
            return null;
        }

        $dataType = $this->typeNodeResolver->resolve($genericType, $nameScope);

        $shape = $dataType->getConstantArrays() |> Arr::first(...);

        if ($shape === null) {
            $keyType = $dataType->getIterableKeyType();
            $valueType = $dataType->getIterableValueType();

            return $this->resolveGenericFluentObjectType($keyType, $valueType);
        }

        $keyTypes = $shape->getKeyTypes();
        $valueTypes = $shape->getValueTypes();

        $names = Collection::make($keyTypes)->map(static function (Type $keyType): ?string {
            $constantString = $keyType->getConstantStrings() |> Arr::first(...);

            return $constantString?->getValue();
        })->filter();

        $explicitMixedType = new MixedType(true);

        $properties = $names->mapWithKeys(static fn (string $name, int $index): array => [
            $name => Arr::get($valueTypes, $index, $explicitMixedType),
        ])->all();

        /** @var list<string> $optionalProperties */
        $optionalProperties = $names->filter(static function (string $name, int $index) use ($shape): bool {
            return $shape->isOptionalKey($index);
        })->values()->all();

        return TypeCombinator::intersect(
            new ObjectShapeType($properties, $optionalProperties),
            $this->resolveGenericFluentObjectType(new StringType, $explicitMixedType)
        );
    }

    /**
     * @phpstan-assert-if-true GenericTypeNode $typeNode
     */
    private function isFluentOfNodeType(TypeNode $typeNode): bool
    {
        if (! $typeNode instanceof GenericTypeNode) {
            return false;
        }

        $typeNodeType = $typeNode->type;

        return $typeNodeType instanceof IdentifierTypeNode && $typeNodeType->name === 'fluent-of';
    }

    private function resolveGenericFluentObjectType(Type $keyType, Type $valueType): GenericObjectType
    {
        $types = [$keyType, $valueType];

        return new GenericObjectType(Fluent::class, $types);
    }
}
