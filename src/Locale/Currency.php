<?php

namespace Mpietrucha\Laravel\Essentials\Locale;

use Mpietrucha\Laravel\Essentials\Enums\Contracts\CurrencyInterface;
use Mpietrucha\Laravel\Essentials\Locale\Concerns\InteractsWithEnum;

abstract class Currency
{
    /**
     * @use InteractsWithEnum<CurrencyInterface>
     */
    use InteractsWithEnum;

    /** @phpstan-ignore class.notFound */
    protected static string $enum = \App\Models\Currency::class;

    protected static string $interface = CurrencyInterface::class;
}
