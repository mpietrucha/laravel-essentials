<?php

namespace Mpietrucha\Laravel\Essentials\Eloquent\Models;

use Brick\Money\Money;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Mpietrucha\Laravel\Essentials\Enums\DiscountType;
use Mpietrucha\Laravel\Essentials\Facade;
use Mpietrucha\Support\Enum;
use Mpietrucha\Support\Exception\RuntimeException;

/**
 * @phpstan-type DiscountBuilder Builder<static>
 */
class Discount extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'type',
        'price',
        'discount_percentage',
        'quantity',
        'quantity_used',
        'notes',
        'active_from',
        'active_to',
    ];

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

    /**
     * @return class-string<DiscountType>
     */
    final public static function getDiscountType(): string
    {
        return Enum::backed(
            config()->string('essentials.discounts.type'),
            DiscountType::class
        );
    }

    #[\Override]
    final public function getTable(): string
    {
        return config()->string('essentials.discounts.table');
    }

    /**
     * @return numeric-string
     */
    public function calculate(Money $money): string
    {
        // wip
        return '100';
    }

    /**
     * @return MorphTo<Model, $this>
     */
    final public function discountable(): MorphTo
    {
        return static::getMorphName() |> $this->morphTo(...);
    }

    /**
     * @param  DiscountBuilder  $builder
     * @return DiscountBuilder
     */
    #[Scope]
    protected function active(Builder $builder): Builder
    {
        return $builder->where(static function (Builder $builder): void {
            $builder->promotion();

            $builder->orWhere(static fn (Builder $builder) => $builder->bundle());
        });
    }

    /**
     * @param  DiscountBuilder  $builder
     * @return DiscountBuilder
     */
    #[Scope]
    protected function promotion(Builder $builder): Builder
    {
        return $builder->where(static function (Builder $builder): void {
            $builder->where('type', self::getDiscountType()::Promotion);

            $builder->where(static function (Builder $builder): void {
                $builder->whereNotNull('price');
                $builder->orWhereNotNull('discount_percentage');
            });

            $builder->validDates();
        });
    }

    /**
     * @param  DiscountBuilder  $builder
     * @return DiscountBuilder
     */
    #[Scope]
    protected function bundle(Builder $builder): Builder
    {
        return $builder->where(static function (Builder $builder): void {
            $builder->where('type', self::getDiscountType()::Bundle);

            $builder->whereNotNull('quantity');
            $builder->whereNotNull('quantity_used');
            $builder->whereNotNull('price');

            $builder->validDates();
        });
    }

    /**
     * @param  DiscountBuilder  $builder
     * @return DiscountBuilder
     */
    #[Scope]
    protected function validDates(Builder $builder): Builder
    {
        $builder->where(static function (Builder $builder): void {
            $builder->whereNull(
                $activeFrom = 'active_from'
            )->orWhereNowOrPast($activeFrom);
        });

        $builder->where(static function (Builder $builder): void {
            $builder->whereNull(
                $activeTo = 'active_to'
            )->orWhereNowOrFuture($activeTo);
        });

        return $builder;
    }

    /**
     * @return array<string, string>
     */
    #[\Override]
    protected function casts(): array
    {
        return [
            'type' => self::getDiscountType(),
        ];
    }
}
