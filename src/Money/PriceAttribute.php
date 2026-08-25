<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Money;

abstract class PriceAttribute extends MoneyAttribute
{
    public static function getPrice(?string $indicator = null): string
    {
        $price = 'price';

        return $indicator === null ? $price : sprintf('%s_%s', $indicator, $price);
    }

    public static function getConvertedPrice(?string $indicator = null): string
    {
        $price = static::getPrice($indicator);

        return sprintf('converted_%s', $price);
    }

    public static function getDiscountedPrice(?string $indicator = null): string
    {
        $price = static::getPrice($indicator);

        return sprintf('discounted_%s', $price);
    }

    public static function getConvertedDiscountedPrice(?string $indicator = null): string
    {
        return static::getDiscountedPrice($indicator) |> static::getConvertedPrice(...);
    }

    public static function getReferencePrice(?string $indicator = null): string
    {
        $price = static::getPrice($indicator);

        return sprintf('reference_%s', $price);
    }

    public static function getNormalizedPrice(?string $indicator = null): string
    {
        $price = static::getPrice($indicator);

        return sprintf('normalized_%s', $price);
    }
}
