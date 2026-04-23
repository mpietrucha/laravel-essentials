<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Enums\Contracts;

use Brick\Math\RoundingMode;
use Brick\Money\Context;
use Brick\Money\Money;

interface CurrencyInterface extends LocaleInterface
{
    public function symbol(): string;

    public function money(mixed $money, ?Context $context = null, ?RoundingMode $roundingMode = null): Money;

    public function convert(mixed $money, mixed $currency, ?Context $context = null, ?RoundingMode $roundingMode = null): Money;
}
