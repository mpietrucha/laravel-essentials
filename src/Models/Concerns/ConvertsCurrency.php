<?php

namespace Mpietrucha\Laravel\Essentials\Models\Concerns;

use Brick\Money\Money;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Mpietrucha\Laravel\Essentials\Enums\Contracts\CurrencyInterface;

trait ConvertsCurrency
{
    /**
     * @return Attribute<null|string, never>
     */
    protected function appPrice(string $price = 'price', string $currency = 'currency'): Attribute
    {
        return Attribute::get(function () use ($price, $currency) {
            $price = $this->$price;
            $currency = $this->$currency;

            if (! is_numeric($price)) {
                return null;
            }

            if ($currency instanceof CurrencyInterface) {
                $currency = $currency->code();
            }

            if (! is_string($currency)) {
                return null;
            }

            $value = match (true) {
                is_int($price) => Money::ofMinor($price, $currency),
                is_float($price),
                is_string($price) => Money::of($price, $currency),
            };

            dd($value);
        });
    }
}
