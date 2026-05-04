<?php

namespace Mpietrucha\Laravel\Essentials\Eloquent\Models\Discount;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Mpietrucha\Laravel\Essentials\Eloquent\Casts\Attribute;
use Mpietrucha\Support\Exception\LogicException;

/**
 * @property null|string $name
 * @property null|string $notes
 * @property null|int $limit
 * @property null|int $limit_used
 */
class Quota extends Phase
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'notes',
        'limit',
        'limit_used',
    ];

    /**
     * @return class-string<static>
     */
    public static function getModel(): string
    {
        /** @phpstan-ignore return.type */
        return config()->string('essentials.discounts.quota.model');
    }

    #[\Override]
    public function getTable(): string
    {
        return config()->string('essentials.discounts.quota.table');
    }

    public function isLimited(): bool
    {
        if ($this->limit === null) {
            return false;
        }

        return $this->limit_used !== null;
    }

    final public function isUnlimited(): bool
    {
        return ! $this->isLimited();
    }

    public function isAvailable(): bool
    {
        if ($this->isUnlimited()) {
            return true;
        }

        return $this->limit_used < $this->limit;
    }

    final public function isExceeded(): bool
    {
        if ($this->isUnlimited()) {
            return false;
        }

        return $this->limit_used >= $this->limit;
    }

    #[\Override]
    public function isActive(): bool
    {
        return $this->isAvailable() && parent::isActive();
    }

    public function use(): static
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
    protected function usage(): Attribute
    {
        return Attribute::get(function (): ?string {
            if ($this->isUnlimited()) {
                return null;
            }

            return sprintf('%s/%s', $this->limit, $this->limit_used);
        });
    }

    /**
     * @param  Builder<static>  $builder
     */
    #[Scope]
    protected function available(Builder $builder): void
    {
        $builder->whereNull($limit = 'limit');
        $builder->whereNull($limitUsed = 'limit_used');

        $builder->orWhere(static function (Builder $builder) use ($limit, $limitUsed): void {
            $builder->whereNotNull($limit);
            $builder->whereNotNull($limitUsed);

            $builder->whereColumn($limitUsed, '<', $limit);
        });
    }

    /**
     * @param  Builder<static>  $builder
     */
    #[Scope]
    #[\Override]
    protected function active(Builder $builder): void
    {
        parent::active($builder);

        $builder->available();
    }
}
