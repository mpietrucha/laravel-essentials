<?php

namespace Mpietrucha\Laravel\Essentials\Mixins;

use Illuminate\Database\Eloquent\Builder;

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
}
