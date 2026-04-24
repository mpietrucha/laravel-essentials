<?php

namespace Mpietrucha\Laravel\Essentials\Eloquent\Models\Concerns;

use Brick\Math\RoundingMode;
use Brick\Money\Context;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Model;
use Mpietrucha\Laravel\Essentials\Eloquent\Casts\Attribute;
use Mpietrucha\Laravel\Essentials\Locale\Currency;
use Mpietrucha\Laravel\Essentials\Money\CurrencyConverter;
use Mpietrucha\Laravel\Essentials\Money\MoneyFactory;
use Throwable;

/**
 * @phpstan-require-extends Model
 */
trait HasMoney
{
    use HasAttributeMutator;

    protected static string $defaultMoneyAttribute = 'price';

    protected static string $defaultMoneyCurrencyAttribute = 'currency';

    public static function getDefaultMoneyAttribute(): string
    {
        return static::$defaultMoneyAttribute;
    }

    public static function getDefaultMoneyCurrencyAttribute(): string
    {
        return static::$defaultMoneyCurrencyAttribute;
    }

    /**
     * @return Attribute<null|Money, never>
     */
    protected function money(?string $moneyAttribute = null, ?string $currencyAttribute = null, ?Context $context = null, ?RoundingMode $roundingMode = null): Attribute
    {
        $moneyAttribute ??= static::getDefaultMoneyAttribute();
        $currencyAttribute ??= static::getDefaultMoneyCurrencyAttribute();

        return Attribute::get(function () use ($moneyAttribute, $currencyAttribute, $context, $roundingMode): ?Money {
            $money = $this->$moneyAttribute;
            $currency = $this->$currencyAttribute;

            try {
                return MoneyFactory::from($money, $currency, $context, $roundingMode);
            } catch (Throwable) {
                return null;
            }
        });
    }

    /**
     * @return Attribute<null|Money, never>
     */
    protected function convertedMoney(?string $moneyAttribute = null, ?string $currencyAttribute = null, mixed $targetCurrency = null, ?Context $context = null, ?RoundingMode $roundingMode = null): Attribute
    {
        return Attribute::get(function () use ($moneyAttribute, $currencyAttribute, $targetCurrency, $context, $roundingMode): ?Money {
            $money = $this->money($moneyAttribute, $currencyAttribute, $context, $roundingMode)->value();

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
