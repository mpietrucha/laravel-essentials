<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Locale;

use Mpietrucha\Laravel\Essentials\Locale\Concerns\InteractsWithEnum;
use Mpietrucha\Laravel\Essentials\Locale\Enums\Contracts\CurrencyInterface;

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
