<?php

declare(strict_types=1);

use Mpietrucha\Laravel\Essentials\Locale\Enums\Contracts\LocaleInterface;

enum InteractsWithLocale: string implements LocaleInterface
{
    use Mpietrucha\Laravel\Essentials\Locale\Enums\Concerns\InteractsWithLocale;
}
