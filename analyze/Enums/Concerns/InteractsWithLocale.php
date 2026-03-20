<?php

use Mpietrucha\Laravel\Essentials\Enums\Contracts\LocaleInterface;

enum InteractsWithLocale: string implements LocaleInterface
{
    use Mpietrucha\Laravel\Essentials\Enums\Concerns\InteractsWithLocale;
}
