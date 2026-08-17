<?php

declare(strict_types=1);

use Mpietrucha\Laravel\Essentials\Discounts\Models\Discount;
use Mpietrucha\Laravel\Essentials\Discounts\Models\Quota;

return [
    'locale' => [
        'currency' => env('ESSENTIALS_APP_CURRENCY'),
    ],

    'discounts' => [
        'enabled' => true,

        'quota' => [
            'table' => 'discount_quotas',

            'model' => Quota::class,
        ],

        'discount' => [
            'table' => 'discounts',

            'model' => Discount::class,
        ],
    ],
];
