<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials;

use Mpietrucha\Laravel\Essentials\Enums\Contracts\LocaleInterface;
use Mpietrucha\Laravel\Essentials\Locale\Concerns\InteractsWithEnum;

abstract class Locale
{
    use InteractsWithEnum;

    /** @phpstan-ignore class.notFound */
    protected static string $enum = \App\Enums\Locale::class;

    /**
     * @var class-string<LocaleInterface>
     */
    protected static string $interface = LocaleInterface::class;
}
