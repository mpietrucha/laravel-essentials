<?php

namespace Mpietrucha\Laravel\Essentials\Eloquent\Models;

use Brick\Math\RoundingMode;
use Brick\Money\Context;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Mpietrucha\Laravel\Essentials\Eloquent\Casts\Attribute;
use Mpietrucha\Laravel\Essentials\Eloquent\Models\Concerns\HasMoney;
use Mpietrucha\Laravel\Essentials\Facade;
use Mpietrucha\Laravel\Essentials\Money\MoneyFactory;
use Mpietrucha\Support\Exception\LogicException;
use Mpietrucha\Support\Exception\RuntimeException;
use Throwable;

/**
 * @phpstan-type DiscountBuilder Builder<static>
 *
 * @property int $id
 * @property int|null $quantity
 * @property int|null $quantity_used
 * @property string|null $price
 * @property int|null $discount_percentage
 * @property string|null $notes
 * @property Carbon|null $active_from
 * @property Carbon|null $active_to
 * @property Carbon|null $finished_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model $discountable
 * @property-read int|float $discount_multiplier
 */
class Discount extends Model
{
    use HasMoney;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'price',
        'discount_percentage',
        'quantity',
        'quantity_used',
        'notes',
        'active_from',
        'active_to',
        'finished_at',
    ];

    protected bool $hasMergedPriceCast = false;

    final public static function enabled(): bool
    {
        return config()->boolean('essentials.discounts.enabled');
    }

    final public static function disabled(): bool
    {
        return ! self::enabled();
    }

    /**
     * @return class-string<static>
     */
    final public static function getModel(): string
    {
        $model = config()->string('essentials.discounts.model');

        $discount = static::class;

        return match (true) {
            is_a($model, $discount, true) => $model,
            default => null
        } ?? RuntimeException::throw('Discount model must be instance of `%s`', $discount);
    }

    final public static function getModelFacade(): string
    {
        return self::getModel() |> Facade::for(...);
    }

    public static function getMorphName(): string
    {
        return 'discountable';
    }

    #[\Override]
    final public function getTable(): string
    {
        return config()->string('essentials.discounts.table');
    }

    public function isBundle(): bool
    {
        if ($this->price === null) {
            return false;
        }

        if ($this->quantity === null) {
            return false;
        }

        return $this->quantity_used !== null;
    }

    public function isPromotion(): bool
    {
        if ($this->quantity !== null || $this->quantity_used !== null) {
            return false;
        }

        if ($this->price === null) {
            return false;
        }

        return $this->discount_percentage !== null;
    }

    public function hasValidDates(): bool
    {
        if ($this->finished_at instanceof Carbon) {
            return false;
        }

        $validTo = $this->active_to?->isNowOrFuture() ?? true;
        $validFrom = $this->active_from?->isNowOrPast() ?? true;

        return $validTo && $validFrom;
    }

    final public function hasInvalidDates(): bool
    {
        return ! $this->hasValidDates();
    }

    public function isBundleActive(): bool
    {
        if ($this->isPromotion()) {
            return false;
        }

        if ($this->hasInvalidDates()) {
            return false;
        }

        return $this->quantity_used < $this->quantity;
    }

    public function isPromotionActive(): bool
    {
        if ($this->isBundle()) {
            return false;
        }

        return $this->hasValidDates();
    }

    public function isActive(): bool
    {
        if ($this->isBundleActive()) {
            return true;
        }

        return $this->isPromotionActive();
    }

    /**
     * @return null|numeric-string
     */
    public function calculate(mixed $originalPrice, ?string $originalPriceCast = null, mixed $currency = null, ?Context $context = null, ?RoundingMode $roundingMode = null): ?string
    {
        try {
            $originalPrice = MoneyFactory::from($originalPrice, $currency, $context, $roundingMode);
        } catch (Throwable) {
            return null;
        }

        $this->ensureMergedPriceCast($originalPriceCast);

        $discountedPrice = $this->money(
            static::getMoneyAttribute(),
            $originalPrice->getCurrency(),
            $context,
            $roundingMode,
        )->value();

        if ($this->isBundleActive()) {
            return $discountedPrice?->getAmount()->toString();
        }

        if ($this->isPromotionActive() === false) {
            return null;
        }

        $discountedPrice ??= $originalPrice->multipliedBy(
            $this->discount_multiplier,
            $roundingMode ?? RoundingMode::HalfUp
        );

        return $discountedPrice->getAmount()->toString();
    }

    public function finish(): static
    {
        $this->finished_at ??= now();

        return $this;
    }

    public function incrementBundleUsage(): static
    {
        if ($this->isBundleActive() === false) {
            LogicException::throw('Only bundles can increment usage');
        }

        $this->quantity_used++;

        if ($this->isBundleActive() === false) {
            $this->finish();
        }

        return $this;
    }

    /**
     * @return MorphTo<Model, $this>
     */
    final public function discountable(): MorphTo
    {
        return static::getMorphName() |> $this->morphTo(...);
    }

    /**
     * @return Attribute<int|float, never>
     */
    protected function discountMultiplier(): Attribute
    {
        return Attribute::get(function (): float|int {
            $discountPercentage = $this->discount_percentage;

            if ($discountPercentage === null) {
                return 1;
            }

            return (100 - $discountPercentage) / 100;
        });
    }

    /**
     * @param  DiscountBuilder  $builder
     */
    #[Scope]
    protected function bundle(Builder $builder): void
    {
        $builder->where(static function (Builder $builder): void {
            $builder->whereNotNull('quantity');
            $builder->whereNotNull('quantity_used');
            $builder->whereNotNull('price');
        });
    }

    /**
     * @param  DiscountBuilder  $builder
     */
    #[Scope]
    protected function promotion(Builder $builder): void
    {
        $builder->where(static function (Builder $builder): void {
            $builder->whereNull('quantity');
            $builder->whereNull('quantity_used');

            $builder->whereNotNull('price');
            $builder->orWhereNotNull('discount_percentage');
        });
    }

    /**
     * @param  DiscountBuilder  $builder
     */
    #[Scope]
    protected function validDates(Builder $builder): void
    {
        $builder->whereNull('finished_at');

        $builder->where(static function (Builder $builder): void {
            $activeFrom = 'active_from';

            $builder->whereNull($activeFrom);

            $builder->orWhereNowOrPast($activeFrom);
        });

        $builder->where(static function (Builder $builder): void {
            $activeTo = 'active_to';

            $builder->whereNull($activeTo);

            $builder->orWhereNowOrFuture($activeTo);
        });
    }

    /**
     * @param  DiscountBuilder  $builder
     */
    #[Scope]
    protected function activeBundle(Builder $builder): void
    {
        $builder->where(static function (Builder $builder): void {
            $builder->bundle();

            $builder->validDates();

            $builder->whereColumn('quantity_used', '<', 'quantity');
        });
    }

    /**
     * @param  DiscountBuilder  $builder
     */
    #[Scope]
    protected function activePromotion(Builder $builder): void
    {
        $builder->where(static function (Builder $builder): void {
            $builder->promotion();

            $builder->validDates();
        });
    }

    /**
     * @param  DiscountBuilder  $builder
     */
    #[Scope]
    protected function active(Builder $builder): void
    {
        $builder->where(static function (Builder $builder): void {
            $builder->activePromotion();

            $builder->orWhere(static fn (Builder $builder) => $builder->activeBundle());
        });
    }

    /**
     * @return array<string, string>
     */
    #[\Override]
    protected function casts(): array
    {
        return [
            'active_from' => 'datetime',
            'active_to' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    protected function ensureMergedPriceCast(?string $priceCast): void
    {
        if ($this->hasMergedPriceCast) {
            return;
        }

        if ($priceCast === null) {
            return;
        }

        $this->hasMergedPriceCast = true;

        $this->mergeCasts([
            static::getMoneyAttribute() => $priceCast,
        ]);
    }
}
