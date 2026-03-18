<?php

use Mpietrucha\Laravel\Essentials\Enums\Contracts\CurrencyInterface;

enum InteractsWithCurrency: string implements CurrencyInterface
{
    use Mpietrucha\Laravel\Essentials\Enums\Concerns\InteractsWithCurrency;
}
