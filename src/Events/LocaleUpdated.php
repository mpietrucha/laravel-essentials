<?php

namespace Mpietrucha\Laravel\Essentials\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Mpietrucha\Laravel\Essentials\Enums\Contracts\LocaleInterface;

class LocaleUpdated
{
    use Dispatchable;

    public function __construct(protected LocaleInterface $locale, protected LocaleInterface $previous)
    {
    }

    public function locale(): LocaleInterface
    {
        return $this->locale;
    }

    public function previous(): LocaleInterface
    {
        return $this->previous;
    }
}
