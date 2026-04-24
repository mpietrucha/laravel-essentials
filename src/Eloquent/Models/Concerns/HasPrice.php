<?php

namespace Mpietrucha\Laravel\Essentials\Eloquent\Models\Concerns;

use Brick\Math\RoundingMode;
use Brick\Money\Context;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Arr;
use Mpietrucha\Laravel\Essentials\Eloquent\Casts\Attribute;
use Mpietrucha\Laravel\Essentials\Eloquent\Models\Discount;

/**
 * @phpstan-require-extends Model
 */
trait HasPrice
{
    use HasMoney;

    public static function getDefaultPriceAttribute(): string
    {
        return static::getDefaultMoneyAttribute();
    }

    public static function getDefaultPriceCurrencyAttribute(): string
    {
        return static::getDefaultMoneyCurrencyAttribute();
    }

    final public static function getDefaultDiscountRelationName(): string
    {
        return 'discount';
    }

    final public static function getDefaultDiscountsRelationName(): string
    {
        return 'discounts';
    }

    /**
     * @return MorphOne<Discount, $this>
     */
    final public function discount(): MorphOne
    {
        $morphOne = $this->morphOne(Discount::getModel(), Discount::getMorphName());

        return $morphOne->active();
    }

    /**
     * @return MorphMany<Discount, $this>
     */
    final public function discounts(): MorphMany
    {
        return $this->morphMany(Discount::getModel(), Discount::getMorphName());
    }

    /**
     * @return Attribute<null|numeric-string, never>
     */
    protected function convertedPrice(?string $priceAttribute = null, ?string $currencyAttribute = null, mixed $targetCurrency = null, ?Context $context = null, ?RoundingMode $roundingMode = null): Attribute
    {
        $priceAttribute ??= static::getDefaultPriceAttribute();
        $currencyAttribute ??= static::getDefaultPriceCurrencyAttribute();

        return Attribute::get(function () use ($priceAttribute, $currencyAttribute, $targetCurrency, $context, $roundingMode): ?string {
            $money = $this->convertedMoney($priceAttribute, $currencyAttribute, $targetCurrency, $context, $roundingMode)->value();

            if ($money === null) {
                return null;
            }

            return $money->getAmount()->toString();
        });
    }

    /**
     * @return Attribute<null|numeric-string, never>
     */
    protected function discountedPrice(?string $discountRelation = null, ?string $priceAttribute = null, ?string $currencyAttribute = null, ?Context $context = null, ?RoundingMode $roundingMode = null): Attribute
    {
        $priceAttribute ??= static::getDefaultPriceAttribute();
        $discountRelation ??= static::getDefaultDiscountRelationName();

        return Attribute::get(function () use ($discountRelation, $priceAttribute, $currencyAttribute, $context, $roundingMode): ?string {
            if (Discount::disabled()) {
                return null;
            }

            $discountedPrice = $this->money($priceAttribute, $currencyAttribute, $context, $roundingMode)->value();

            if ($discountedPrice === null) {
                return null;
            }

            /** @var null|Discount $discount */
            $discount = $this->loadMissing($discountRelation)->$discountRelation;

            if (! $discount instanceof Discount) {
                return $discountedPrice->getAmount()->toString();
            }

            /** @var null|string $priceCast */
            $priceCast = Arr::get($this->getCasts(), $priceAttribute);

            return $discount->calculate($discountedPrice, $priceCast, $discountedPrice->getCurrency(), $context, $roundingMode);
        });
    }

    /**
     * @return Attribute<null|numeric-string, never>
     */
    protected function convertedDiscountedPrice(string $discountedPriceAttribute = 'discounted_price', ?string $currencyAttribute = null, mixed $targetCurrency = null, ?Context $context = null, ?RoundingMode $roundingMode = null): Attribute
    {
        return $this->convertedPrice($discountedPriceAttribute, $currencyAttribute, $targetCurrency, $context, $roundingMode);
    }

    /**
     * @return Attribute<null|numeric-string, never>
     */
    protected function referencePrice(?string $discountRelation = null, ?string $priceAttribute = null, ?string $currencyAttribute = null, ?Context $context = null, ?RoundingMode $roundingMode = null): Attribute
    {
        return Attribute::get(function () use ($discountRelation, $priceAttribute, $currencyAttribute, $context, $roundingMode): ?string {
            if (Discount::disabled()) {
                return null;
            }

            $referencePrice = $this->money($priceAttribute, $currencyAttribute, $context, $roundingMode)->value();

            if ($referencePrice === null) {
                return null;
            }

            $discountedPrice = $this->discountedPrice(
                $discountRelation,
                $priceAttribute,
                $currencyAttribute,
                $context,
                $roundingMode
            )->value();

            $referencePrice = $referencePrice->getAmount()->toString();

            return $referencePrice === $discountedPrice ? null : $referencePrice;
        });
    }
}
