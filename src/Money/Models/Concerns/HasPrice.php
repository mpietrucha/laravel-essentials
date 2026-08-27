<?php

namespace Mpietrucha\Laravel\Essentials\Money\Models\Concerns;

use Brick\Math\RoundingMode;
use Brick\Money\Context;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Mpietrucha\Laravel\Essentials\Eloquent\Casts\Attribute;
use Mpietrucha\Laravel\Essentials\Eloquent\Models\Concerns\DeclaresDecoratedAttributes;
use Mpietrucha\Laravel\Essentials\Locale\Currency;
use Mpietrucha\Laravel\Essentials\Money\CurrencyConverter;
use Mpietrucha\Laravel\Essentials\Money\Models\Discount;
use Mpietrucha\Laravel\Essentials\Money\PriceAttribute;

/**
 * @phpstan-require-extends Model
 */
trait HasPrice
{
    use DeclaresDecoratedAttributes;
    use HasMoney;

    public static function getDefaultPriceAttribute(): string
    {
        return PriceAttribute::getPrice();
    }

    public static function getDefaultDiscountedPriceAttribute(): string
    {
        return PriceAttribute::getDiscountedPrice();
    }

    public static function getDefaultNormalizedPriceAttribute(): string
    {
        return PriceAttribute::getNormalizedPrice();
    }

    public static function getDefaultPriceCurrencyAttribute(): string
    {
        return static::getDefaultMoneyCurrencyAttribute();
    }

    public static function getDefaultNormalizedPriceTargetCurrency(): mixed
    {
        return Currency::enum()::default();
    }

    /**
     * @return MorphOne<Discount, $this>
     */
    public function discount(): MorphOne
    {
        $morphOne = $this->morphOne(Discount::getModel(), $discountable = Discount::getMorphName());

        $morphOne->without($discountable);
        $morphOne->chaperone($discountable);

        return $morphOne->active();
    }

    /**
     * @return MorphMany<Discount, $this>
     */
    public function discounts(): MorphMany
    {
        $morphMany = $this->morphMany(Discount::getModel(), $discountable = Discount::getMorphName());

        $morphMany->without($discountable);
        $morphMany->chaperone($discountable);

        return $morphMany->valid();
    }

    public function getPriceAttributeValue(?string $priceAttribute = null): mixed
    {
        return $this->getMoneyAttributeValue($priceAttribute ?? static::getDefaultPriceAttribute());
    }

    public function getPriceCurrencyAttributeValue(?string $currencyAttribute = null): mixed
    {
        $currencyAttribute ??= static::getDefaultPriceCurrencyAttribute();

        return $this->getMoneyAttributeValue($currencyAttribute);
    }

    public function castPriceAttribute(mixed $price, ?string $priceAttribute = null): mixed
    {
        $priceAttribute ??= static::getDefaultPriceAttribute();

        return $this->castMoneyAttribute($price, $priceAttribute);
    }

    public function getPrice(?string $priceAttribute = null, ?string $currencyAttribute = null, ?Context $context = null, ?RoundingMode $roundingMode = null): ?Money
    {
        $priceAttribute ??= static::getDefaultPriceAttribute();
        $currencyAttribute ??= static::getDefaultPriceCurrencyAttribute();

        return $this->getMoney($priceAttribute, $currencyAttribute, $context, $roundingMode);
    }

    public function getConvertedPrice(mixed $targetCurrency = null, ?string $priceAttribute = null, ?string $currencyAttribute = null, ?Context $context = null, ?RoundingMode $roundingMode = null): ?Money
    {
        $priceAttribute ??= static::getDefaultPriceAttribute();
        $currencyAttribute ??= static::getDefaultPriceCurrencyAttribute();

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

        $discountedPriceAttribute ??= static::getDefaultDiscountedPriceAttribute();

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

    public function getNormalizedPrice(?string $discountedPriceAttribute = null, ?string $priceAttribute = null, ?string $currencyAttribute = null, mixed $targetCurrency = null, ?Context $context = null, ?RoundingMode $roundingMode = null): ?Money
    {
        $targetCurrency ??= static::getDefaultNormalizedPriceTargetCurrency();

        $convertedDiscountedPrice = $this->getConvertedDiscountedPrice($targetCurrency, $discountedPriceAttribute, $currencyAttribute, $context, $roundingMode);

        if ($convertedDiscountedPrice === null) {
            return $this->getDiscountedPrice($priceAttribute, $currencyAttribute, $context, $roundingMode);
        }

        return $convertedDiscountedPrice;
    }

    public function normalizePrice(?string $normalizedPriceAttribute = null, ?string $discountedPriceAttribute = null, ?string $priceAttribute = null, ?string $currencyAttribute = null, mixed $targetCurrency = null, ?Context $context = null, ?RoundingMode $roundingMode = null): static
    {
        $normalizedPriceAttribute ??= static::getDefaultNormalizedPriceAttribute();

        $normalizedPrice = $this->getNormalizedPrice($discountedPriceAttribute, $priceAttribute, $currencyAttribute, $targetCurrency, $context, $roundingMode);

        $this->$normalizedPriceAttribute = $normalizedPrice?->getAmount()->toFloat();

        return $this;
    }

    /**
     * @return Attribute<null|numeric-string, never>
     */
    protected function price(): Attribute
    {
        return $this->getPrice(...) |> Attribute::getMoneyAmount(...);
    }

    /**
     * @return Attribute<null|numeric-string, never>
     */
    protected function convertedPrice(): Attribute
    {
        return $this->getConvertedPrice(...) |> Attribute::getMoneyAmount(...);
    }

    /**
     * @return Attribute<null|numeric-string, never>
     */
    protected function discountedPrice(): Attribute
    {
        return $this->getDiscountedPrice(...) |> Attribute::getMoneyAmount(...);
    }

    /**
     * @return Attribute<null|numeric-string, never>
     */
    protected function convertedDiscountedPrice(): Attribute
    {
        return $this->getConvertedDiscountedPrice(...) |> Attribute::getMoneyAmount(...);
    }

    /**
     * @return Attribute<null|numeric-string, never>
     */
    protected function referencePrice(): Attribute
    {
        return $this->getReferencePrice(...) |> Attribute::getMoneyAmount(...);
    }

    /**
     * @param  Builder<static>  $builder
     */
    #[Scope]
    protected function whereNormalizedPrice(Builder $builder, mixed $price, ?string $operator = null, mixed $sourceCurrency = null, mixed $targetCurrency = null, ?string $normalizedPriceAttribute = null, ?Context $context = null, ?RoundingMode $roundingMode = null): void
    {
        $targetCurrency ??= static::getDefaultNormalizedPriceTargetCurrency();

        $normalizedPrice = CurrencyConverter::convert($price, $sourceCurrency, $targetCurrency, $context, $roundingMode);

        $normalizedPriceAttribute ??= static::getDefaultNormalizedPriceAttribute();

        $builder->where($normalizedPriceAttribute, $operator, $normalizedPrice->getAmount()->toFloat());
    }

    protected static function bootHasPrice(): void
    {
        static::saving(static function (self $hasPrice): void {
            $hasPrice->normalizePrice();
        });
    }
}
