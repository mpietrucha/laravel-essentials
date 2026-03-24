<?php

namespace Mpietrucha\Laravel\Essentials\Models\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use RoundingMode;

/**
 * @phpstan-require-extends Model
 */
trait HasConvertedPrice
{
    use HasMoney;

    /**
     * @return Attribute<null|numeric-string, never>
     */
    protected function convertedPrice(?string $amountAttribute = null, ?string $currencyAttribute = null, mixed $targetCurrency = null, ?RoundingMode $roundingMode = null): Attribute
    {
        return Attribute::get(function () use ($amountAttribute, $currencyAttribute, $targetCurrency, $roundingMode): ?string {
            $money = $this->convertedMoney($amountAttribute, $currencyAttribute, $targetCurrency, $roundingMode)->get |> value(...);

            if ($money === null) {
                return null;
            }

            return $money->getAmount()->toString();
        });
    }
}
