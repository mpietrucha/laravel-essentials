<?php

declare(strict_types=1);

use Mpietrucha\Laravel\Essentials\Money\Models\Discount;
use Mpietrucha\Laravel\Essentials\Money\Models\Discount\Quota;

return [
    'locale' => [
        'currency' => env('ESSENTIALS_APP_CURRENCY'),
    ],

    'money' => [
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

            'touch_stale_phases' => [
                'schedule' => env('ESSENTIALS_TOUCH_STALE_PHASES_SCHEDULE', true),
            ],
        ],
        'normalize_prices' => [
            'schedule' => env('ESSENTIALS_NORMALIZE_PRICES_SCHEDULE', true),
            'log_activity' => env('ESSENTIALS_NORMALIZE_PRICES_LOG_ACTIVITY', false),
        ],
    ],
];
