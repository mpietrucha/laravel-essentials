<?php

namespace Mpietrucha\Laravel\Essentials\Enums\Contracts;

use Brick\Money\Money;

interface CurrencyInterface extends LocaleInterface
{
    public function symbol(): string;

    public function money(mixed $amount): Money;

    public function convert(mixed $amount, mixed $currency): Money;
}
