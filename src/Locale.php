<?php

namespace Mpietrucha\Laravel\Essentials;

use Mpietrucha\Laravel\Essentials\Locale\Concerns\InteractsWithEnum;

abstract class Locale
{
    use InteractsWithEnum;

    /** @phpstan-ignore class.notFound */
    protected static string $enum = \App\Models\Locale::class;
}
