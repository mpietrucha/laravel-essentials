<?php

declare(strict_types=1);

use Mpietrucha\Laravel\Essentials\Eloquent\Models\Discount;

return [
    'locale' => [
        'currency' => env('ESSENTIALS_APP_CURRENCY'),
    ],

    'discounts' => [
        'enabled' => true,

        'table' => 'discounts',

        'model' => Discount::class,
    ],
];
