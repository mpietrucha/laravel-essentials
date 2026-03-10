<?php

namespace Mpietrucha\Laravel\Essentials\Locale\Concerns;

use Mpietrucha\Utility\Enum;
use Mpietrucha\Utility\Utilizer\Concerns\Utilizable;

/**
 * @phpstan-require-implements \Mpietrucha\Laravel\Essentials\Locale\Contracts\InteractsWithEnumInterface
 */
trait InteractsWithEnum
{
    use Utilizable\Strings;

    public static function enum(): ?string
    {
        $enum = static::utilize();

        if (Enum::incompatible($enum)) {
            return null;
        }

        return $enum;
    }
}
