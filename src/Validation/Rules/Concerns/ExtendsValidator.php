<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Validation\Rules\Concerns;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Mpietrucha\Laravel\Essentials\Translations\Qualifiers\KeyQualifier;
use Mpietrucha\Support\ClassNamespace;
use Mpietrucha\Support\Concerns\Compatible;
use Mpietrucha\Support\Str;

/**
 * @phpstan-require-implements ValidationRule
 */
trait ExtendsValidator
{
    use Compatible;

    public static function name(): string
    {
        return ClassNamespace::name(static::class) |> Str::snake(...);
    }

    public static function message(): string
    {
        $name = static::name();

        return KeyQualifier::build($name, 'validation') |> __(...);
    }

    public static function extend(): void
    {
        $name = static::name();

        Validator::extend($name, static fn (string $attribute, mixed $value): bool => static::compatible($value));
    }
}
