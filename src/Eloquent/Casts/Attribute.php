<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Eloquent\Casts;

use Illuminate\Database\Eloquent\Casts\Attribute as IlluminateAttribute;

/**
 * @template TGet of mixed
 * @template TSet of mixed
 *
 * @extends IlluminateAttribute<TGet, TSet>
 *
 * @method static static get(callable|null $get)
 * @method static static set(callable|null $get)
 */
class Attribute extends IlluminateAttribute
{
    /**
     * @return TGet
     */
    public function value(): mixed
    {
        return $this->get |> value(...);
    }
}
