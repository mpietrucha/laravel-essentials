<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Money;

abstract class MoneyAttribute
{
    public static function getCurrency(): string
    {
        return 'currency';
    }
}
