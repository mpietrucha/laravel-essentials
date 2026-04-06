<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Enums\Contracts;

use BackedEnum;
use Mpietrucha\Support\Enums\Contracts\EnumInterface;

interface LocaleInterface extends BackedEnum, EnumInterface
{
    public static function get(): static;

    public static function set(string $value): static;

    public function code(): string;
}
