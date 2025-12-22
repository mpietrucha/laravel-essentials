<?php

namespace Mpietrucha\Laravel\Package\Macro;

use Mpietrucha\Utility\Concerns\Creatable;
use Mpietrucha\Utility\Contracts\CreatableInterface;
use Mpietrucha\Utility\Value;
use Mpietrucha\Utility\Value\Contracts\AttemptInterface;

/**
 * @phpstan-import-type MixedArray from \Mpietrucha\Utility\Arr
 */
class Attempt implements CreatableInterface
{
    use Creatable;

    public function __construct(protected AttemptInterface $adapter)
    {
    }

    public static function build(callable $handler): static
    {
        return Value::attempt($handler) |> static::create(...);
    }

    public function adapter(): AttemptInterface
    {
        return $this->adapter;
    }

    /**
     * @param  MixedArray  $arguments
     */
    public function eval(string $method, array $arguments): mixed
    {
        $result = $this->adapter()->eval($arguments);

        Failure::handle($result, $method);

        return $result->value();
    }
}
