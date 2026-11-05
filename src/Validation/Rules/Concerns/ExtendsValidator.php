<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Validation\Rules\Concerns;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator as IlluminateValidator;
use Illuminate\Foundation\Events\LocaleUpdated;
use Illuminate\Support\Facades\Event;
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

        return KeyQualifier::build($name, 'essentials::validation') |> __(...);
    }

    /**
     * @param  array<int, int|string>  $parameters
     */
    public static function compatibleByExtend(string $attribute, mixed $value, array $parameters, IlluminateValidator $validator): bool
    {
        /** @phpstan-ignore arguments.count */
        return static::compatible($value, $attribute, $parameters, $validator);
    }

    public static function extend(): void
    {
        $name = static::name();
        $message = static::message();

        Validator::extend($name, static::compatibleByExtend(...), $message);
    }

    public static function register(): void
    {
        static::extend();

        Event::listen(LocaleUpdated::class, static::extend(...));
    }
}
