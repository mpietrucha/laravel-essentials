<?php

namespace Mpietrucha\Laravel\Essentials\Events;

use Illuminate\Foundation\Events\Dispatchable;

class CurrencyUpdated
{
    use Dispatchable;

    public function __construct(public string $locale, public ?string $previous = null)
    {
    }
}
