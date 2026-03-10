<?php

namespace Mpietrucha\Laravel\Essentials\Locale\Contracts;

use Mpietrucha\Utility\Utilizer\Contracts\UtilizableInterface;

interface InteractsWithEnumInterface extends UtilizableInterface
{
    /**
     * @return null|class-string<\Mpietrucha\Utility\Enums\Contracts\InteractsWithEnumInterface&\BackedEnum>
     */
    public static function enum(): ?string;
}
