<?php

namespace Mpietrucha\Laravel\Essentials\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Uri;
use League\Uri\Exceptions\SyntaxError;
use Mpietrucha\Laravel\Essentials\Rules\Concerns\ExtendsValidator;
use Mpietrucha\Support\Concerns\Makeable;
use Mpietrucha\Support\Str;

class Domain implements ValidationRule
{
    use ExtendsValidator;
    use Makeable;

    public static function compatible(mixed $value): bool
    {
        if (! is_string($value)) {
            return false;
        }

        try {
            Uri::of()->withHost($value);
        } catch (SyntaxError) {
            return false;
        }

        return Str::doesntStartWith(Str::lower($value), 'www.');
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (static::compatible($value)) {
            return;
        }

        static::message() |> $fail(...);
    }
}
