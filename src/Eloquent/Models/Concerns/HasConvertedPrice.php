<?php

namespace Mpietrucha\Laravel\Essentials\Eloquent\Models\Concerns;

use Brick\Math\RoundingMode;
use Brick\Money\Context;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * @phpstan-require-extends Model
 */
trait HasConvertedPrice
{
    use HasMoney;

    /**
     * @return Attribute<null|numeric-string, never>
     */
    protected function convertedPrice(?string $amountAttribute = null, ?string $currencyAttribute = null, mixed $targetCurrency = null, ?Context $context = null, ?RoundingMode $roundingMode = null): Attribute
    {
        return Attribute::get(function () use ($amountAttribute, $currencyAttribute, $targetCurrency, $context, $roundingMode): ?string {
            $money = $this->convertedMoney($amountAttribute, $currencyAttribute, $targetCurrency, $context, $roundingMode)->get |> value(...);

            if ($money === null) {
                return null;
            }

            return $money->getAmount()->toString();
        });
    }
}
