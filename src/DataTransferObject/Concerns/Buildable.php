<?php

namespace Mpietrucha\Laravel\Essentials\DataTransferObject\Concerns;

use Illuminate\Support\Fluent;
use Mpietrucha\Laravel\Essentials\DataTransferObject;
use Mpietrucha\Support\Exception\BadMethodCallException;
use Mpietrucha\Support\Exception\RuntimeException;
use Throwable;

/**
 * @phpstan-require-extends DataTransferObject
 *
 * @template TInputData of array<string, mixed>
 */
trait Buildable
{
    /**
     * @param  TInputData  $data
     */
    public static function fromArray(array $data): static
    {
        return fluent($data) |> static::fromFluent(...);
    }

    /**
     * @param  fluent-of<TInputData>  $data
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
