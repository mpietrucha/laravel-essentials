<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Eloquent\Casts;

use Brick\Money\Money;
use Closure;
use Illuminate\Database\Eloquent\Casts\Attribute as IlluminateAttribute;

/**
 * @template TGet of mixed
 * @template TSet of mixed
 *
 * @extends IlluminateAttribute<TGet, TSet>
 *
 * @method static static get(callable|null $get)
 * @method static static set(callable|null $get)
 *
 * @phpstan-type MoneyAmountAttribute static<null|numeric-string, never>
 */
class Attribute extends IlluminateAttribute
{
    /**
     * @return MoneyAmountAttribute
     */
    public static function getMoneyAmount(Closure $get): static
    {
        /** @var MoneyAmountAttribute */
        return static::get(static function () use ($get): ?string {
            $money = value($get);

            if (! $money instanceof Money) {
                return null;
            }

            return $money->getAmount()->toString();
        });
    }

    /**
     * @return TGet
     */
    public function value(): mixed
    {
        return $this->get |> value(...);
    }
}
