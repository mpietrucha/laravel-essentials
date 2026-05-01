<?php

namespace Mpietrucha\Laravel\Essentials\Eloquent\Models\Discount\Concerns;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
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

    public function hasActiveTimestamps(): bool
    {
        if ($this->finished_at instanceof Carbon) {
            return false;
        }

        $validTo = $this->active_to?->isNowOrFuture() ?? true;
        $validFrom = $this->active_from?->isNowOrPast() ?? true;

        return $validTo && $validFrom;
    }

    final public function hasInactiveTimestamps(): bool
    {
        return ! $this->hasActiveTimestamps();
    }

    public function finish(): static
    {
        $this->finished_at ??= now();

        return $this;
    }

    /**
     * @param  Builder<static>  $builder
     */
    #[Scope]
    protected function withActiveTimestamps(Builder $builder): void
    {
        $builder->whereNull('finished_at');

        $builder->where(static function (Builder $builder): void {
            $builder->whereNull($activeFrom = 'active_from');

            $builder->orWhereNowOrPast($activeFrom);
        });

        $builder->where(static function (Builder $builder): void {
            $builder->whereNull($activeTo = 'active_to');

            $builder->orWhereNowOrFuture($activeTo);
        });
    }

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
