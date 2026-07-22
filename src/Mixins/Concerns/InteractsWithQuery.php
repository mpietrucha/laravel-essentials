<?php

namespace Mpietrucha\Laravel\Essentials\Mixins\Concerns;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Mpietrucha\Support\Str;

trait InteractsWithQuery
{
    public function updateColumn(string $column, mixed $value): int
    {
        $attributes = [
            $column => $value,
        ];

        return $this->update($attributes);
    }

    public function buildQualifiedColumn(string $column, ?string $table = null): string
    {
        if (Str::contains($column, '.')) {
            return $column;
        }

        $table = match (true) {
            is_string($table) => $table,
            $this instanceof QueryBuilder,
            $this instanceof EloquentBuilder => $this->from,
            default => Str::none(),
        };

        if ($table instanceof Expression) {
            $table = $this->getGrammar() |> $table->getValue(...);
        }

        $table = Str::afterLast((string) $table, 'as') |> Str::trim(...) |> Str::nullWhenEmpty(...);

        if ($table === null) {
            return $column;
        }

        return sprintf('%s.%s', $table, $column);
    }
}
