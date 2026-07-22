<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Mixins;

use Illuminate\Database\Query\Builder;
use Mpietrucha\Laravel\Essentials\Mixins\Concerns\InteractsWithQuery;

/**
 * @phpstan-require-extends Builder
 */
trait QueryBuilderMixin
{
    use InteractsWithQuery;
}
