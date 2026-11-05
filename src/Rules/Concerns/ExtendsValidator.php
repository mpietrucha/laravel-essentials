<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Rules\Concerns;

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

        return KeyQualifier::build($name, 'validator') |> __(...);
    }

    public static function extend(): void
    {
        $name = static::name();
        $validator = static::compatible(...);

        Validator::extend($name, $validator);
    }
}
