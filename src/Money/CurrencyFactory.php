<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Money;

use Brick\Money\Currency as BrickCurrency;
use Brick\Money\Money;
use Mpietrucha\Laravel\Essentials\Locale\Currency;
use Mpietrucha\Laravel\Essentials\Locale\Enums\Contracts\CurrencyInterface;
use Mpietrucha\Support\Exception\InvalidArgumentException;

abstract class CurrencyFactory
{
    public static function from(mixed $currency = null): string
    {
        if ($currency instanceof Money) {
            $currency = $currency->getCurrency();
        }

        return match (true) {
            $currency instanceof BrickCurrency => $currency->getCurrencyCode(),
            $currency instanceof CurrencyInterface => $currency->code(),
            is_string($currency) => $currency,
            is_null($currency) => Currency::get()->code(),
            default => InvalidArgumentException::throw('Unexpected currency of type `%s`', get_debug_type($currency))
        };
    }
}
