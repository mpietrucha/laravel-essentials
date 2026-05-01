<?php

namespace Mpietrucha\Laravel\Essentials\Eloquent\Models\Discount;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Mpietrucha\Laravel\Essentials\Eloquent\Models\Discount\Concerns\InteractsWithTimestamps;
use Mpietrucha\Support\Exception\LogicException;

/**
 * @property null|string $name
 * @property null|string $notes
 * @property null|int $quantity
 * @property null|int $quantity_used
 *
 * @phpstan-type ModelClass class-string<static>
 */
class Quota extends Model
{
    use InteractsWithTimestamps;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'notes',
        'quantity',
        'quantity_used',
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

    public function isActive(): bool
    {
        if ($this->hasInactiveTimestamps()) {
            return false;
        }

        if ($this->quantity === null) {
            return true;
        }

        if ($this->quantity_used === null) {
            return true;
        }

        return $this->quantity_used < $this->quantity;
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

        $this->quantity_used++;

        if ($this->isInactive()) {
            $this->finish();
        }

        return $this;
    }

    /**
     * @param  Builder<static>  $builder
     */
    #[Scope]
    protected function active(Builder $builder): void
    {
        $builder->withActiveTimestamps();

        $builder->whereNull($quantity = 'quantity');
        $builder->whereNull($quantityUsed = 'quantity_used');

        $builder->orWhere(static function (Builder $builder) use ($quantity, $quantityUsed): void {
            $builder->whereNotNull($quantity);
            $builder->whereNotNull($quantityUsed);

            $builder->whereColumn($quantityUsed, '<', $quantity);
        });
    }
}
