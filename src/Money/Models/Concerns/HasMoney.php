<?php

namespace Mpietrucha\Laravel\Essentials\Money\Models\Concerns;

use Brick\Math\RoundingMode;
use Brick\Money\Context;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Model;
use Mpietrucha\Laravel\Essentials\Locale\Currency;
use Mpietrucha\Laravel\Essentials\Money\CurrencyConverter;
use Mpietrucha\Laravel\Essentials\Money\Models\Attributes\MoneyAttribute;
use Mpietrucha\Laravel\Essentials\Money\MoneyFactory;
use Mpietrucha\Support\Arr;

/**
 * @phpstan-require-extends Model
 */
trait HasMoney
{
    public static function getDefaultMoneyCurrencyAttribute(): string
    {
        return MoneyAttribute::getCurrency();
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

        $cast = $this->hasCast($moneyAttribute) ? $this->getCastType($moneyAttribute) : null;

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

        return MoneyFactory::tryFrom(
            $this->castMoneyAttribute($money, $moneyAttribute),
            $currency,
            $context,
            $roundingMode,
        );
    }

    public function getConvertedMoney(string $moneyAttribute, ?string $currencyAttribute = null, mixed $targetCurrency = null, ?Context $context = null, ?RoundingMode $roundingMode = null): ?Money
    {
        $money = $this->getMoney($moneyAttribute, $currencyAttribute, $context, $roundingMode);

        if ($money === null) {
            return null;
        }

        return CurrencyConverter::tryConvert($money, $targetCurrency ?? Currency::get());
    }
}
