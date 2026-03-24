<?php

namespace Mpietrucha\Laravel\Essentials\Money;

use Brick\Math\RoundingMode;
use Brick\Money\Context;
use Brick\Money\Money;
use Mpietrucha\Support\Exception\InvalidArgumentException;

abstract class MoneyFactory
{
    public static function from(mixed $amount, mixed $currency = null, ?Context $context = null, ?RoundingMode $roundingMode = null): Money
    {
        $currency = CurrencyConverter::currency($currency, $amount);

        if ($currency === null) {
            /** @var Money $amount */
            return $amount;
        }

        $roundingMode ??= RoundingMode::Unnecessary;

        return match (true) {
            is_string($amount),
            is_float($amount) => Money::of($amount, $currency, $context, $roundingMode),
            is_int($amount) => Money::ofMinor($amount, $currency, $context, $roundingMode),
            default => InvalidArgumentException::throw('Unexpected amount of type `%s`', get_debug_type($amount))
        };
    }
}
