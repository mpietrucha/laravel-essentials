<?php

namespace Mpietrucha\Laravel\Essentials\Models\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasConvertedPrice
{
    use HasMoney;

    /**
     * @return Attribute<null|numeric-string, never>
     */
    protected function convertedPrice(?string $amountAttribute = null, ?string $currencyAttribute = null, mixed $targetCurrency = null): Attribute
    {
        return Attribute::get(function () use ($amountAttribute, $currencyAttribute, $targetCurrency): ?string {
            $money = $this->convertedMoney($amountAttribute, $currencyAttribute, $targetCurrency)->get |> value(...);

            if ($money === null) {
                return null;
            }

            return $money->getAmount()->toString();
        });
    }
}
