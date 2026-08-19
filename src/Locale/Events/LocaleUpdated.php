<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Locale\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Mpietrucha\Laravel\Essentials\Locale\Enums\Contracts\LocaleInterface;

class LocaleUpdated
{
    use Dispatchable;

    public function __construct(protected LocaleInterface $locale, protected LocaleInterface $previous)
    {
    }
}
