<?php

namespace Mpietrucha\Laravel\Essentials\Eloquent\Models\Concerns;

use Brick\Math\RoundingMode;
use Brick\Money\Context;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Mpietrucha\Laravel\Essentials\Eloquent\Casts\Attribute;
use Mpietrucha\Laravel\Essentials\Eloquent\Models\Discount;

/**
 * @phpstan-require-extends Model
 */
trait HasPrice
{
    use DeclaresDecoratedAttributes;
    use HasMoney;

    protected static string $defaultPriceAttribute = 'price';

    public static function getDefaultPriceAttribute(): string
    {
        return static::$defaultPriceAttribute;
    }

    /**
     * @return MorphOne<Discount, $this>
     */
    public function discount(): MorphOne
    {
        $morphOne = $this->morphOne(Discount::getModel(), Discount::getMorphName());

        return $morphOne->active();
    }

    /**
     * @return MorphMany<Discount, $this>
     */
    public function discounts(): MorphMany
    {
        $morphMany = $this->morphMany(Discount::getModel(), Discount::getMorphName());

        return $morphMany->valid();
    }

    public function getPriceAttributeValue(?string $priceAttribute = null): mixed
    {
        $priceAttribute ??= static::getDefaultPriceAttribute();

        return $this->getMoneyAttributeValue($priceAttribute);
    }

    public function castPriceAttribute(mixed $price, ?string $priceAttribute = null): mixed
    {
        $priceAttribute ??= static::getDefaultPriceAttribute();

        return $this->castMoneyAttribute($price, $priceAttribute);
    }

    public function getPrice(?string $priceAttribute = null, ?string $currencyAttribute = null, ?Context $context = null, ?RoundingMode $roundingMode = null): ?Money
    {
        $priceAttribute ??= static::getDefaultPriceAttribute();

        return $this->getMoney($priceAttribute, $currencyAttribute, $context, $roundingMode);
    }

    public function getConvertedPrice(mixed $targetCurrency = null, ?string $priceAttribute = null, ?string $currencyAttribute = null, ?Context $context = null, ?RoundingMode $roundingMode = null): ?Money
    {
        $priceAttribute ??= static::getDefaultPriceAttribute();

        return $this->getConvertedMoney($priceAttribute, $currencyAttribute, $targetCurrency, $context, $roundingMode);
    }

    public function getDiscountedPrice(?string $priceAttribute = null, ?string $currencyAttribute = null, ?Context $context = null, ?RoundingMode $roundingMode = null): ?Money
    {
        if (Discount::disabled()) {
            return null;
        }

        $price = $this->getPrice($priceAttribute, $currencyAttribute, $context, $roundingMode);

        if ($price === null) {
            return null;
        }

        /** @var null|Discount $discount */
        $discount = $this->loadMissing($discountRelation = 'discount')->$discountRelation;

        if (! $discount instanceof Discount) {
            return $price;
        }

        return $discount->calculate($price, null, $priceAttribute, $context, $roundingMode);
    }

    public function getConvertedDiscountedPrice(mixed $targetCurrency = null, ?string $discountedPriceAttribute = null, ?string $currencyAttribute = null, ?Context $context = null, ?RoundingMode $roundingMode = null): ?Money
    {
        if (Discount::disabled()) {
            return null;
        }

        $discountedPriceAttribute ??= 'discounted_price';

        return $this->getConvertedPrice($targetCurrency, $discountedPriceAttribute, $currencyAttribute, $context, $roundingMode);
    }

    public function getReferencePrice(?string $priceAttribute = null, ?string $currencyAttribute = null, ?Context $context = null, ?RoundingMode $roundingMode = null): ?Money
    {
        if (Discount::disabled()) {
            return null;
        }

        $price = $this->getPrice($priceAttribute, $currencyAttribute, $context, $roundingMode);

        if ($price === null) {
            return null;
        }

        $discountedPrice = $this->getDiscountedPrice($priceAttribute, $currencyAttribute, $context, $roundingMode);

        if ($discountedPrice === null) {
            return null;
        }

        return $price->isEqualTo($discountedPrice) ? null : $price;
    }

    /**
     * @return Attribute<null|numeric-string, never>
     */
    protected function price(?string $priceAttribute = null, ?string $currencyAttribute = null, ?Context $context = null, ?RoundingMode $roundingMode = null): Attribute
    {
        return Attribute::getMoneyAmount(fn (): ?Money => $this->getPrice(
            $priceAttribute,
            $currencyAttribute,
            $context,
            $roundingMode,
        ));
    }

    /**
     * @return Attribute<null|numeric-string, never>
     */
    protected function convertedPrice(mixed $targetCurrency = null, ?string $priceAttribute = null, ?string $currencyAttribute = null, ?Context $context = null, ?RoundingMode $roundingMode = null): Attribute
    {
        return Attribute::getMoneyAmount(fn (): ?Money => $this->getConvertedPrice(
            $targetCurrency,
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
     * @return Attribute<null|numeric-string, never>
     */
    protected function convertedDiscountedPrice(mixed $targetCurrency = null, ?string $discountedPriceAttribute = null, ?string $currencyAttribute = null, ?Context $context = null, ?RoundingMode $roundingMode = null): Attribute
    {
        return Attribute::getMoneyAmount(fn (): ?Money => $this->getconvertedDiscountedPrice(
            $targetCurrency,
            $discountedPriceAttribute,
            $currencyAttribute,
            $context,
            $roundingMode,
        ));
    }

    /**
     * @return Attribute<null|numeric-string, never>
     */
    protected function referencePrice(?string $priceAttribute = null, ?string $currencyAttribute = null, ?Context $context = null, ?RoundingMode $roundingMode = null): Attribute
    {
        return Attribute::getMoneyAmount(fn (): ?Money => $this->getReferencePrice(
            $priceAttribute,
            $currencyAttribute,
            $context,
            $roundingMode,
        ));
    }
}
