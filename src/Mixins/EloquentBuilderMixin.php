<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Mixins;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Mpietrucha\Laravel\Essentials\Mixins\Concerns\InteractsWithQuery;

/**
 * @phpstan-require-extends Builder
 */
trait EloquentBuilderMixin
{
    use InteractsWithQuery;

    public function whereRelationship(string $column, mixed $value, ?string $relationshipColumn = null, ?string $operator = null): static
    {
        $relationship = $relationshipColumn ? Str::relationshipName($relationshipColumn) : Str::relationshipName($column);

        if ($relationship === null) {
            return $this->where($column, $operator, $value);
        }

        return $this->whereRelation($relationship, $column, $operator, $value);
    }
}
