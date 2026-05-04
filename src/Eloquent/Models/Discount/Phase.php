<?php

namespace Mpietrucha\Laravel\Essentials\Eloquent\Models\Discount;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mpietrucha\Laravel\Essentials\Eloquent\Casts\Attribute;
use Mpietrucha\Laravel\Essentials\Eloquent\Models\Concerns\DeclaresDecoratedAttributes;
use Mpietrucha\Laravel\Essentials\Eloquent\Models\Discount\Concerns\InteractsWithTimestamps;

abstract class Phase extends Model
{
    use DeclaresDecoratedAttributes;
    use InteractsWithTimestamps;
    use SoftDeletes;

    public function isValid(): bool
    {
        return true;
    }

    final public function isInvalid(): bool
    {
        return ! $this->isValid();
    }

    public function isFinished(): bool
    {
        return $this->finished_at instanceof Carbon;
    }

    public function isActive(): bool
    {
        if ($this->isFinished()) {
            return false;
        }

        $validTo = $this->active_to?->isNowOrFuture() ?? true;
        $validFrom = $this->active_from?->isNowOrPast() ?? true;

        return $validTo && $validFrom;
    }

    final public function isInactive(): bool
    {
        return ! $this->isActive();
    }

    public function isScheduled(): bool
    {
        if ($this->isFinished()) {
            return false;
        }

        return $this->active_from?->isFuture() ?? false;
    }

    public function finish(): static
    {
        $this->finished_at ??= now();

        return $this;
    }

    /**
     * @return Attribute<string, never>
     */
    protected function status(): Attribute
    {
        return Attribute::make(fn (): string => match (true) { /** @phpstan-ignore match.unhandled */
            $this->isInvalid() => 'invalid',
            $this->isFinished() => 'finished',
            $this->isActive() => 'active',
            $this->isScheduled() => 'scheduled',
        });
    }

    /**
     * @param  Builder<static>  $builder
     */
    #[Scope]
    protected function finished(Builder $builder): void
    {
        $builder->whereNotNull('finished_at');
    }

    /**
     * @param  Builder<static>  $builder
     */
    #[Scope]
    protected function active(Builder $builder): void
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

    /**
     * @param  Builder<static>  $builder
     */
    #[Scope]
    protected function scheduled(Builder $builder): void
    {
        $builder->whereNull('finished_at');

        $builder->where(static function (Builder $builder): void {
            $builder->whereNotNull($activeFrom = 'active_from');

            $builder->orWhereFuture($activeFrom);
        });
    }
}
