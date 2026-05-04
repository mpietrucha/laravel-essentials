<?php

namespace Mpietrucha\Laravel\Essentials\Eloquent\Models\Discount;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Mpietrucha\Laravel\Essentials\Eloquent\Casts\Attribute;
use Mpietrucha\Laravel\Essentials\Eloquent\Models\Concerns\DeclaresDecoratedAttributes;
use Mpietrucha\Laravel\Essentials\Eloquent\Models\Discount\Concerns\InteractsWithTimestamps;
use Mpietrucha\Support\Exception\LogicException;

/**
 * @property null|string $name
 * @property null|string $notes
 * @property null|int $limit_total
 * @property null|int $limit_used
 *
 * @phpstan-type ModelClass class-string<static>
 */
class Quota extends Model
{
    use DeclaresDecoratedAttributes;
    use InteractsWithTimestamps;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'notes',
        'limit_total',
        'limit_used',
    ];

    /**
     * @return ModelClass
     */
    public static function getModel(): string
    {
        /** @var ModelClass */
        return config()->string('essentials.discounts.quota.model');
    }

    #[\Override]
    public function getTable(): string
    {
        return config()->string('essentials.discounts.quota.table');
    }

    public function hasValidLimit(): bool
    {
        if ($this->limit_total === null) {
            return true;
        }

        return $this->limit_used !== null;
    }

    final public function hasInvalidLimit(): bool
    {
        return ! $this->hasValidLimit();
    }

    public function hasExceededLimit(): bool
    {
        if ($this->hasInvalidLimit()) {
            return false;
        }

        return $this->limit_used >= $this->limit_total;
    }

    final public function hasRemainingLimit(): bool
    {
        if ($this->hasInvalidLimit()) {
            return false;
        }

        return $this->limit_used < $this->limit_total;
    }

    public function isActive(): bool
    {
        if ($this->hasExceededLimit()) {
            return false;
        }

        return $this->hasActiveTimestamps();
    }

    final public function isInactive(): bool
    {
        return ! $this->isActive();
    }

    public function incrementUsage(): static
    {
        if ($this->isInactive()) {
            LogicException::throw('Only active usages can be incremented');
        }

        $this->limit_used++;

        if ($this->isInactive()) {
            $this->finish();
        }

        return $this;
    }

    /**
     * @return Attribute<int|string, never>
     */
    protected function limit(): Attribute
    {
        return Attribute::get(function (): ?string {
            if ($this->hasInvalidLimit()) {
                return null;
            }

            return sprintf('%s/%s', $this->limit_total, $this->limit_used);
        });
    }

    /**
     * @param  Builder<static>  $builder
     */
    #[Scope]
    protected function withValidLimit(Builder $builder): void
    {
        $builder->whereNull($limitTotal = 'limit_total');
        $builder->whereNull($limitUsed = 'limit_used');

        $builder->orWhere(static function (Builder $builder) use ($limitTotal, $limitUsed): void {
            $builder->whereNotNull($limitTotal);
            $builder->whereNotNull($limitUsed);

            $builder->whereColumn($limitUsed, '<', $limitTotal);
        });
    }

    /**
     * @param  Builder<static>  $builder
     */
    #[Scope]
    protected function active(Builder $builder): void
    {
        $builder->withValidLimit();
        $builder->withActiveTimestamps();
    }
}
