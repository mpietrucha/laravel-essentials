<?php

namespace Mpietrucha\Laravel\Essentials\Money\Models\Concerns;

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
    protected static string $defaultMoneyCurrencyAttribute = 'currency';

    public static function getDefaultMoneyCurrencyAttribute(): string
    {
        return static::$defaultMoneyCurrencyAttribute;
    }

    public function getMoneyAttributeValue(string $moneyAttribute): mixed
    {
        $attributes = $this->getAttributes();

        return Arr::get($attributes, $moneyAttribute, fn (): mixed => data_get($this, $moneyAttribute));
    }

    public function getMoneyCurrencyAttributeValue(?string $currencyAttribute = null): mixed
    {
        $currencyAttribute ??= static::getDefaultMoneyCurrencyAttribute();

        return $this->getMoneyAttributeValue($currencyAttribute);
    }

    public function castMoneyAttribute(mixed $money, string $moneyAttribute): mixed
    {
        if (! is_scalar($money)) {
            return $money;
        }

        $cast = rescue(fn () => $this->getCastType($moneyAttribute), report: false);

        return match ($cast) {
            'int',
            'integer' => (int) $money,
            default => (string) $money,
        };
    }

    public function getMoney(string $moneyAttribute, ?string $currencyAttribute = null, ?Context $context = null, ?RoundingMode $roundingMode = null): ?Money
    {
        $money = $this->getMoneyAttributeValue($moneyAttribute);
        $currency = $this->getMoneyCurrencyAttributeValue($currencyAttribute);

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
