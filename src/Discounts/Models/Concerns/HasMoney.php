<?php

namespace Mpietrucha\Laravel\Essentials\Discounts\Models\Concerns;

use Brick\Math\RoundingMode;
use Brick\Money\Context;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Mpietrucha\Laravel\Essentials\Locale\Currency;
use Mpietrucha\Laravel\Essentials\Money\CurrencyConverter;
use Mpietrucha\Laravel\Essentials\Money\MoneyFactory;
use Throwable;

/**
 * @phpstan-require-extends Model
 */
trait HasMoney
{
    protected static string $defaultCurrencyAttribute = 'currency';

    public static function getDefaultCurrencyAttribute(): string
    {
        return static::$defaultCurrencyAttribute;
    }

    public function getMoneyAttributeValue(string $attribute): mixed
    {
        $attributes = $this->getAttributes();

        return Arr::get($attributes, $attribute, function () use ($attribute) {
            return data_get($this, $attribute);
        });
    }

    public function getCurrencyAttributeValue(?string $attribute = null): mixed
    {
        $attribute ??= static::getDefaultCurrencyAttribute();

        return $this->getMoneyAttributeValue($attribute);
    }

    public function castMoneyAttribute(mixed $money, string $attribute): mixed
    {
        if (! is_scalar($money)) {
            return $money;
        }

        $cast = rescue(
            fn () => $this->getCastType($attribute),
            report: false
        );

        return match ($cast) {
            'int',
            'integer' => (int) $money,
            default => (string) $money,
        };
    }

    public function getMoney(string $moneyAttribute, ?string $currencyAttribute = null, ?Context $context = null, ?RoundingMode $roundingMode = null): ?Money
    {
        $money = $this->getMoneyAttributeValue($moneyAttribute);
        $currency = $this->getCurrencyAttributeValue($currencyAttribute);

        try {
            return MoneyFactory::from(
                $this->castMoneyAttribute($money, $moneyAttribute),
                $currency,
                $context,
                $roundingMode,
            );
        } catch (Throwable) {
            return null;
        }
    }

    public function getConvertedMoney(string $moneyAttribute, ?string $currencyAttribute = null, mixed $targetCurrency = null, ?Context $context = null, ?RoundingMode $roundingMode = null): ?Money
    {
        $money = $this->getMoney($moneyAttribute, $currencyAttribute, $context, $roundingMode);

        if ($money === null) {
            return null;
        }

        $targetCurrency ??= Currency::get();

        try {
            $convertedMoney = CurrencyConverter::convert($money, $targetCurrency);
        } catch (Throwable) {
            return null;
        }

        $sourceCurrency = $money->getCurrency();

        if ($convertedMoney->getCurrency()->is($sourceCurrency)) {
            return null;
        }

        return $convertedMoney;
    }
}
