<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Locale\Enums\Contracts;

use BackedEnum;
use Closure;
use Mpietrucha\Support\Enums\Contracts\EnumInterface;

interface LocaleInterface extends BackedEnum, EnumInterface
{
    public static function get(): static;

    public static function set(mixed $value): static;

    public static function with(mixed $value, Closure $callback): mixed;

    public function code(): string;

    public function activate(): void;
}
