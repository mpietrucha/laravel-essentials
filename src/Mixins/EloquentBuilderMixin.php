<?php

namespace Mpietrucha\Laravel\Essentials\Mixins;

use Illuminate\Database\Eloquent\Builder;
use Mpietrucha\Support\Str;

/**
 * @phpstan-require-extends Builder
 */
trait EloquentBuilderMixin
{
    public function updateColumn(string $column, mixed $value): int
    {
        $attributes = [
            $column => $value,
        ];

        return $this->update($attributes);
    }

    /**
     * @param  string|array<string>  $relationships
     */
    public function hasAll(array|string ...$relationships): static
    {
        $this->has(...) |> collect($relationships)->flatten()->each(...);

        return $this;
    }

    public function buildQualifiedColumn(string $column, ?string $table = null): string
    {
        if (Str::contains($column, '.')) {
            return $column;
        }

        return sprintf('%s.%s', $table ?? $this->model->getTable(), $column);
    }
}
