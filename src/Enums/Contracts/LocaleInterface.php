<?php

namespace Mpietrucha\Laravel\Essentials\Enums\Contracts;

use BackedEnum;

interface LocaleInterface extends BackedEnum
{
    public static function get(): static;

    public static function set(string $value): static;
}
