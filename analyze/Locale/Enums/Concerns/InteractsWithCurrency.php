<?php

declare(strict_types=1);

use Mpietrucha\Laravel\Essentials\Locale\Enums\Contracts\CurrencyInterface;

enum InteractsWithCurrency: string implements CurrencyInterface
{
    use Mpietrucha\Laravel\Essentials\Locale\Enums\Concerns\InteractsWithCurrency;
}
