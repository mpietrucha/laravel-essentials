<?php

namespace Mpietrucha\Laravel\Essentials\Eloquent\Models\Concerns;

use Brick\Math\RoundingMode;
use Brick\Money\Context;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
    protected static string $defaultMoneyAmountAttribute = 'price';

    protected static string $defaultMoneyCurrencyAttribute = 'currency';

    /**
     * @return Attribute<null|Money, never>
     */
    protected function money(?string $amountAttribute = null, ?string $currencyAttribute = null, ?Context $context = null, ?RoundingMode $roundingMode = null): Attribute
    {
        $amountAttribute ??= static::$defaultMoneyAmountAttribute;
        $currencyAttribute ??= static::$defaultMoneyCurrencyAttribute;

        return Attribute::get(function () use ($amountAttribute, $currencyAttribute, $context, $roundingMode): ?Money {
            $amount = $this->$amountAttribute;
            $currency = $this->$currencyAttribute;

            try {
                return MoneyFactory::from($amount, $currency, $context, $roundingMode);
            } catch (Throwable) {
                return null;
            }
        });
    }

    /**
     * @return Attribute<null|Money, never>
     */
    protected function convertedMoney(?string $amountAttribute = null, ?string $currencyAttribute = null, mixed $targetCurrency = null, ?Context $context = null, ?RoundingMode $roundingMode = null): Attribute
    {
        return Attribute::get(function () use ($amountAttribute, $currencyAttribute, $targetCurrency, $context, $roundingMode): ?Money {
            $money = $this->money($amountAttribute, $currencyAttribute, $context, $roundingMode)->get |> value(...);

            if ($money === null) {
                return null;
            }

            try {
                $convertedMoney = CurrencyConverter::convert($money, $targetCurrency ?? Currency::get());
            } catch (Throwable) {
                return null;
            }

            if ($money->getCurrency() |> $convertedMoney->getCurrency()->is(...)) {
                return null;
            }

            return $convertedMoney;
        });
    }
}
