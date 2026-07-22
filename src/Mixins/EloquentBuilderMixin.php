<?php

namespace Mpietrucha\Laravel\Essentials\Mixins;

use Illuminate\Database\Eloquent\Builder;
use Mpietrucha\Laravel\Essentials\Mixins\Concerns\InteractsWithQuery;

/**
 * @phpstan-require-extends Builder
 */
trait EloquentBuilderMixin
{
    use InteractsWithQuery;

    /**
     * @param  string|array<string>  $relationships
     */
    public function hasAll(array|string ...$relationships): static
    {
        $this->has(...) |> collect($relationships)->flatten()->each(...);

        return $this;
    }
}
