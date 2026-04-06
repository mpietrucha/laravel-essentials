<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Mpietrucha\Laravel\Essentials\Enums\Contracts\CurrencyInterface;

class CurrencyUpdated
{
    use Dispatchable;

    public function __construct(public CurrencyInterface $currency, public CurrencyInterface $previous)
    {
    }
}
