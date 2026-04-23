<?php

namespace Mpietrucha\Laravel\Essentials\Money;

use Brick\Math\RoundingMode;
use Brick\Money\Context;
use Brick\Money\Money;
use Mpietrucha\Support\Exception\InvalidArgumentException;

abstract class MoneyFactory
{
    public static function from(mixed $money, mixed $currency = null, ?Context $context = null, ?RoundingMode $roundingMode = null): Money
    {
        $currency = CurrencyConverter::currency($currency, $money);

        if ($currency === null) {
            /** @var Money $money */
            return $money;
        }

        $roundingMode ??= RoundingMode::Unnecessary;

        return match (true) {
            is_string($money),
            is_float($money) => Money::of($money, $currency, $context, $roundingMode),
            is_int($money) => Money::ofMinor($money, $currency, $context, $roundingMode),
            default => InvalidArgumentException::throw('Unexpected money of type `%s`', get_debug_type($money))
        };
    }
}
