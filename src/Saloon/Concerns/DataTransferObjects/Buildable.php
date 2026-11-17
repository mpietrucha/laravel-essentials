<?php

namespace Mpietrucha\Laravel\Essentials\Saloon\Concerns\DataTransferObjects;

use Illuminate\Support\Fluent;
use Mpietrucha\Laravel\Essentials\Saloon\DataTransferObjects\DataTransferObject;
use Mpietrucha\Support\Exception\BadMethodCallException;
use Mpietrucha\Support\Exception\RuntimeException;
use Throwable;

/**
 * @phpstan-require-extends DataTransferObject
 */
trait Buildable
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): static
    {
        return fluent($data) |> static::fromFluent(...);
    }

    /**
     * @param  Fluent<string, mixed>  $data
     */
    public static function fromFluent(Fluent $data): static
    {
        BadMethodCallException::throw('Body of a `fromFluent` method must be specified');
    }

    public static function build(mixed ...$arguments): static
    {
        try {
            return static::make(...$arguments);
        } catch (Throwable $throwable) {
            RuntimeException::usePrevious($throwable);

            RuntimeException::throw('Response data object failed to build');
        }
    }
}
