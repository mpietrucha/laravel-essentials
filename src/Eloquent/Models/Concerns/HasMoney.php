<?php

namespace Mpietrucha\Laravel\Essentials\Eloquent\Models\Concerns;

use Brick\Math\RoundingMode;
use Brick\Money\Context;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Model;
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

    public function getMoneyAttributeValue(string $moneyAttribute): mixed
    {
        return data_get($this->getAttributes(), $moneyAttribute);
    }

    public function getCurrencyAttributeValue(?string $currencyAttribute = null): mixed
    {
        $currencyAttribute ??= static::getDefaultCurrencyAttribute();

        return data_get($this, $currencyAttribute);
    }

    public function castMoneyAttribute(mixed $money, string $moneyAttribute): mixed
    {
        if (! is_scalar($money)) {
            return $money;
        }

        $cast = rescue(fn () => $this->getCastType($moneyAttribute));

        if ($cast === 'int' || $cast === 'integer') {
            return (int) $money;
        }

        return (string) $money;
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

        if ($money->getCurrency() |> $convertedMoney->getCurrency()->is(...)) {
            return null;
        }

        return $convertedMoney;
    }
}
