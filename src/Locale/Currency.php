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
    protected static string $enum = \App\Enums\Currency::class;

    /**
     * @var class-string<CurrencyInterface>
     */
    protected static string $interface = CurrencyInterface::class;
}
