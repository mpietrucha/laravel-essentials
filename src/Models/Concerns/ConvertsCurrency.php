<?php

namespace Mpietrucha\Laravel\Essentials\Models\Concerns;

trait ConvertsCurrency
{
    protected function appPrice(string $price = 'price', string $currency = 'currency'): Attribute
    {
        return Attribute::get(function () {

        });
    }
}
