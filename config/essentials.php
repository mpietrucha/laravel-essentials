<?php

declare(strict_types=1);

use Mpietrucha\Laravel\Essentials\Eloquent\Models\Discount;
use Mpietrucha\Laravel\Essentials\Enums\DiscountType;

return [
    'locale' => [
        'currency' => env('ESSENTIALS_APP_CURRENCY'),
    ],

    'discounts' => [
        'enabled' => true,

        'table' => 'discounts',

        'model' => Discount::class,

        'type' => DiscountType::class,
    ],
];
