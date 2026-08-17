<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Money;

class HasPriceAutoloader
{
    public static function register(): void
    {
        spl_autoload_register(static function (string $class): void {

        });
    }
}
