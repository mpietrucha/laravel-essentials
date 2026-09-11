<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Mixins;

use Illuminate\Database\Eloquent\Builder;
use Mpietrucha\Laravel\Essentials\Eloquent\Qualifiers\AttributeQualifier;
use Mpietrucha\Laravel\Essentials\Mixins\Concerns\InteractsWithQuery;

/**
 * @phpstan-require-extends Builder
 */
trait EloquentBuilderMixin
{
    use InteractsWithQuery;

    public function whereRelationship(string $column, mixed $value, ?string $relationship = null, ?string $operator = null): static
    {
        $relationship = AttributeQualifier::relationship($relationship ?? $column);

        if ($relationship === null) {
            return $this->where($column, $operator, $value);
        }

        return $this->whereRelation($relationship, $column, $operator, $value);
    }
}
