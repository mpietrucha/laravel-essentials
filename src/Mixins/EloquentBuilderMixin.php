<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Mixins;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Mpietrucha\Laravel\Essentials\Eloquent\Qualifiers\AttributeQualifier;
use Mpietrucha\Laravel\Essentials\Mixins\Concerns\InteractsWithQuery;

/**
 * @phpstan-require-extends Builder
 */
trait EloquentBuilderMixin
{
    use InteractsWithQuery;

    public function whereRelationship(string $column, mixed $value, ?string $relationship = null, string $operator = '='): static
    {
        $qualifiedColumn = AttributeQualifier::build($column, $relationship);

        $relationship = AttributeQualifier::relationship($qualifiedColumn);

        $column = AttributeQualifier::attribute($qualifiedColumn);

        $constraint = static function (Builder $builder) use ($column, $operator, $value): void {
            if (! is_array($value)) {
                $builder->where($column, $operator, $value);

                return;
            }

            $builder->whereJsonLength($column, count($value));

            Collection::make($value)->each(static fn (mixed $value) => $builder->whereJsonContains($column, $value));
        };

        if ($relationship === null) {
            return $this->where($constraint);
        }

        return $this->whereHas($relationship, $constraint);
    }
}
