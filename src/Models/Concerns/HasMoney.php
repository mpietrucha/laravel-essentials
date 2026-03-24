<?php

namespace Mpietrucha\Laravel\Essentials\Models\Concerns;

use Brick\Money\Money;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Mpietrucha\Laravel\Essentials\Locale\Currency;
use Mpietrucha\Laravel\Essentials\Locale\MoneyBuilder;
use RoundingMode;
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
    protected function money(?string $amountAttribute = null, ?string $currencyAttribute = null): Attribute
    {
        $amountAttribute ??= static::$defaultMoneyAmountAttribute;
        $currencyAttribute ??= static::$defaultMoneyCurrencyAttribute;

        return Attribute::get(function () use ($amountAttribute, $currencyAttribute): ?Money {
            $amount = $this->$amountAttribute;
            $currency = $this->$currencyAttribute;

            try {
                return MoneyBuilder::build($amount, $currency);
            } catch (Throwable) {
                return null;
            }
        });
    }

    /**
     * @return Attribute<null|Money, never>
     */
    protected function convertedMoney(?string $amountAttribute = null, ?string $currencyAttribute = null, mixed $targetCurrency = null, ?RoundingMode $roundingMode = null): Attribute
    {
        return Attribute::get(function () use ($amountAttribute, $currencyAttribute, $targetCurrency, $roundingMode): ?Money {
            $money = $this->money($amountAttribute, $currencyAttribute)->get |> value(...);

            if ($money === null) {
                return null;
            }

            try {
                return MoneyBuilder::convert($money, $targetCurrency ?? Currency::get(), $roundingMode);
            } catch (Throwable) {
                return null;
            }
        });
    }
}
