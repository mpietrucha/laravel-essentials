<?php

namespace Mpietrucha\Laravel\Essentials\Enums\Contracts;

use Mpietrucha\Support\Enums\Contracts\EnumInterface;

interface LocaleInterface extends EnumInterface
{
    public static function get(): static;

    public static function set(string $value): static;
}
