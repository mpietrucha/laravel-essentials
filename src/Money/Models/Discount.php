<?php

namespace Mpietrucha\Laravel\Essentials\Money\Models;

use Brick\Math\RoundingMode;
use Brick\Money\Context;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Mpietrucha\Laravel\Essentials\Eloquent\Casts\Attribute;
use Mpietrucha\Laravel\Essentials\Money\Models\Discount\Phase;
use Mpietrucha\Laravel\Essentials\Money\Models\Discount\Quota;
use Mpietrucha\Laravel\Essentials\Money\MoneyFactory;
use Mpietrucha\Support\Instance;
use Spatie\Activitylog\Traits\LogsActivity;
use Throwable;

/**
 * @property null|string $price
 * @property int|null $discount_percentage
 * @property-read int|float $discount_multiplier
 */
class Discount extends Phase
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'price',
        'discount_percentage',
    ];

    /**
     * @var list<string>
     */
    protected $with = [
        'quota',
        'discountable',
    ];

    public static function enabled(): bool
    {
        return config()->boolean('essentials.money.discounts.enabled');
    }

    final public static function disabled(): bool
    {
        return ! static::enabled();
    }

    /**
     * @return class-string<static>
     */
    public static function getModel(): string
    {
        /** @phpstan-ignore return.type */
        return config()->string('essentials.money.discounts.discount.model');
    }

    final public static function getMorphName(): string
    {
        return 'discountable';
    }

    #[\Override]
    public function getTable(): string
    {
        return config()->string('essentials.money.discounts.discount.table');
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function discountable(): MorphTo
    {
        return self::getMorphName() |> $this->morphTo(...);
    }

    /**
     * @return BelongsTo<Quota, $this>
     */
    public function quota(): BelongsTo
    {
        return Quota::getModel() |> $this->belongsTo(...);
    }

    #[\Override]
    public function isValid(): bool
    {
        return $this->price !== null || $this->discount_percentage !== null;
    }

    #[\Override]
    public function isFinished(): bool
    {
        if (parent::isFinished()) {
            return true;
        }

        return $this->getQuotaRelation()?->isFinished() ?? false;
    }

    #[\Override]
    public function isActive(): bool
    {
        if (parent::isActive() === false) {
            return false;
        }

        return $this->getQuotaRelation()?->isActive() ?? true;
    }

    #[\Override]
    public function isScheduled(): bool
    {
        if (parent::isScheduled()) {
            return true;
        }

        return $this->getQuotaRelation()?->isScheduled() ?? false;
    }

    public function getPrice(?string $priceAttribute = null, ?string $currencyAttribute = null, mixed $currency = null, ?Context $context = null, ?RoundingMode $roundingMode = null): ?Money
    {
        $price = Arr::get($this->getAttributes(), 'price');

        if ($price === null) {
            return null;
        }

        $discountable = $this->getDiscountableRelation();

        if (! $discountable instanceof Model) {
            return null;
        }

        /** @phpstan-ignore method.notFound */
        $currency ??= $discountable->getMoneyCurrencyAttributeValue($currencyAttribute);

        try {
            return MoneyFactory::from(
                /** @phpstan-ignore method.notFound */
                $discountable->castPriceAttribute($price, $priceAttribute),
                $currency,
                $context,
                $roundingMode,
            );
        } catch (Throwable) {
            return null;
        }
    }

    public function getOriginalPrice(?string $priceAttribute = null, ?string $currencyAttribute = null, ?Context $context = null, ?RoundingMode $roundingMode = null): ?Money
    {
        if (self::disabled()) {
            return null;
        }

        /** @phpstan-ignore method.notFound, return.type */
        return $this->getDiscountableRelation()?->getPrice($priceAttribute, $currencyAttribute, $context, $roundingMode);
    }

    public function getDiscountedPrice(?string $priceAttribute = null, ?string $currencyAttribute = null, ?Context $context = null, ?RoundingMode $roundingMode = null): ?Money
    {
        if (self::disabled()) {
            return null;
        }

        $price = $this->getOriginalPrice($priceAttribute, $currencyAttribute, $context, $roundingMode);

        if (! $price instanceof Money) {
            return null;
        }

        return $this->calculate($price, null, $priceAttribute, $context, $roundingMode);
    }

    public function calculate(mixed $price, mixed $currency = null, ?string $priceAttribute = null, ?Context $context = null, ?RoundingMode $roundingMode = null): ?Money
    {
        if (self::disabled() || $price === null) {
            return null;
        }

        try {
            $price = MoneyFactory::from($price, $currency, $context, $roundingMode);
        } catch (Throwable) {
            return null;
        }

        $multiplier = $this->discount_multiplier;

        if ($multiplier < 1) {
            return $price->multipliedBy($multiplier, $roundingMode ?? RoundingMode::HalfUp);
        }

        return $this->getPrice($priceAttribute, null, $price->getCurrency(), $context, $roundingMode);
    }

    /**
     * @return Attribute<null|numeric-string, never>
     */
    protected function price(?string $priceAttribute = null, ?string $currencyAttribute = null, mixed $currency = null, ?Context $context = null, ?RoundingMode $roundingMode = null): Attribute
    {
        return Attribute::getMoneyAmount(fn (): ?Money => $this->getPrice(
            $priceAttribute,
            $currencyAttribute,
            $currency,
            $context,
            $roundingMode,
        ));
    }

    /**
     * @return Attribute<null|numeric-string, never>
     */
    protected function originalPrice(?string $priceAttribute = null, ?string $currencyAttribute = null, ?Context $context = null, ?RoundingMode $roundingMode = null): Attribute
    {
        return Attribute::getMoneyAmount(fn (): ?Money => $this->getOriginalPrice(
            $priceAttribute,
            $currencyAttribute,
            $context,
            $roundingMode,
        ));
    }

    /**
     * @return Attribute<null|numeric-string, never>
     */
    protected function discountedPrice(?string $priceAttribute = null, ?string $currencyAttribute = null, ?Context $context = null, ?RoundingMode $roundingMode = null): Attribute
    {
        return Attribute::getMoneyAmount(fn (): ?Money => $this->getDiscountedPrice(
            $priceAttribute,
            $currencyAttribute,
            $context,
            $roundingMode,
        ));
    }

    /**
     * @return Attribute<int|float, never>
     */
    protected function discountMultiplier(): Attribute
    {
        return Attribute::get(function (): float|int {
            $percentage = $this->discount_percentage;

            if ($percentage === null) {
                return 1;
            }

            return (100 - $percentage) / 100;
        });
    }

    /**
     * @param  Builder<static>  $builder
     */
    #[Scope]
    protected function valid(Builder $builder): void
    {
        $builder->qualifyColumn('price') |> $builder->whereNotNull(...);
        $builder->qualifyColumn('discount_percentage') |> $builder->orWhereNotNull(...);
    }

    /**
     * @param  Builder<static>  $builder
     */
    #[Scope]
    #[\Override]
    protected function finished(Builder $builder, bool $withQuota = true): void
    {
        $builder->where(static function (Builder $builder): void {
            parent::finished($builder);
        });

        if (! $withQuota) {
            return;
        }

        $builder->orWhereHas('quota', static function (Builder $builder): void {
            /** @var Builder<Quota> $builder */
            $builder->finished();
        });
    }

    /**
     * @param  Builder<static>  $builder
     */
    #[Scope]
    #[\Override]
    protected function active(Builder $builder, bool $withQuota = true): void
    {
        parent::active($builder);

        $builder->valid();

        if (! $withQuota) {
            return;
        }

        $builder->where(static function (Builder $builder): void {
            $builder->whereDoesntHave($quota = 'quota');

            $builder->orWhereHas($quota, static function (Builder $builder): void {
                /** @var Builder<Quota> $builder */
                $builder->active();
            });
        });
    }

    /**
     * @param  Builder<static>  $builder
     */
    #[Scope]
    #[\Override]
    protected function scheduled(Builder $builder, bool $withQuota = true): void
    {
        $builder->where(static function (Builder $builder): void {
            parent::scheduled($builder);
        });

        if (! $withQuota) {
            return;
        }

        $builder->orWhereHas('quota', static function (Builder $builder): void {
            /** @var Builder<Quota> $builder */
            $builder->scheduled();
        });
    }

    final protected function getQuotaRelation(): ?Quota
    {
        /** @var null|Quota */
        return $this->loadMissing($quotaRelation = 'quota')->$quotaRelation;
    }

    final protected function getDiscountableRelation(): ?Model
    {
        /** @var null|Model */
        $discountable = $this->loadMissing($discountableRelation = 'discountable')->$discountableRelation;

        if (! $discountable instanceof Model) {
            return null;
        }

        if ($this->isActive()) {
            return $discountable;
        }

        if (Instance::traits($discountable)->doesntContain(LogsActivity::class)) {
            return $discountable;
        }

        /** @phpstan-ignore method.notFound */
        $activities = $discountable->activities();

        /** @var Relation<Model, Model, Model> $activities */
        $activity = $activities
            ->latest()
            /** @phpstan-ignore argument.type */
            ->where($discountable->getCreatedAtColumn(), '<=', $this->{$this->getCreatedAtColumn()})
            ->first();

        if (! $activity instanceof Model) {
            return null;
        }

        /** @phpstan-ignore method.notFound, argument.type */
        return $activity->getExtraProperty('attributes', []) |> $discountable->newFromBuilder(...);
    }

    protected static function booted(): void
    {
        static::saved(function (self $discount): void {
            $discountable = $discount->discountable;

            if (! $discountable instanceof Model) {
                return;
            }

            $discountable->unsetRelation('discount')->save();
        });
    }
}
