<?php

namespace Mpietrucha\Laravel\Package\Macro;

use Mpietrucha\Utility\Concerns\Creatable;
use Mpietrucha\Utility\Contracts\CreatableInterface;
use Mpietrucha\Utility\Str;
use Mpietrucha\Utility\Throwable\Contracts\InteractsWithThrowableInterface;
use Mpietrucha\Utility\Type;
use Mpietrucha\Utility\Value\Contracts\ResultInterface;

class Failure implements CreatableInterface
{
    use Creatable;

    public function __construct(protected InteractsWithThrowableInterface $throwable, protected string $method)
    {
    }

    public function throw(): void
    {
        $throwable = $this->throwable();

        $this->message() |> $throwable->message(...);

        $throwable->align(5);

        $throwable->throw();
    }

    public static function handle(ResultInterface $result, string $method): void
    {
        $throwable = $result->throwable();

        if (Type::null($throwable)) {
            return;
        }

        static::create($throwable, $method)->throw();
    }

    protected function message(): string
    {
        $value = $this->throwable()->value();

        $file = Str::sprintf('{closure:%s:%s}', $value->getFile(), $value->getLine());

        $method = $this->method();

        $message = $value->getMessage() |> Str::of(...);

        return Str::comma() |> $message->replace($file, $method)->beforeLast(...);
    }

    protected function throwable(): InteractsWithThrowableInterface
    {
        return $this->throwable;
    }

    protected function method(): string
    {
        return $this->method;
    }
}
