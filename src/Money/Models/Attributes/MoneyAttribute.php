<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Money\Models\Attributes;

abstract class MoneyAttribute
{
    public static function getCurrency(): string
    {
        return 'currency';
    }
}
