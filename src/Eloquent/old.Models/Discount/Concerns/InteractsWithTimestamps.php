<?php

namespace Mpietrucha\Laravel\Essentials\Eloquent\Models\Discount\Concerns;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @phpstan-require-extends Model
 *
 * @internal
 *
 * @property Carbon|null $active_from
 * @property Carbon|null $active_to
 * @property Carbon|null $finished_at
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
trait InteractsWithTimestamps
{
    use SoftDeletes;

    protected function initializeInteractsWithTimestamps(): void
    {
        $this->mergeFillable([
            'active_from',
            'active_to',
            'finished_at',
        ]);

        $this->mergeCasts([
            'active_from' => 'datetime',
            'active_to' => 'datetime',
            'finished_at' => 'datetime',
        ]);
    }
}
