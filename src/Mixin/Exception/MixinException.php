<?php

namespace Mpietrucha\Laravel\Package\Mixin\Exception;

use Mpietrucha\Utility\Throwable\InvalidArgumentException;

class MixinException extends InvalidArgumentException
{
    public function initialize(): void
    {
        'Expected a trait name or object instance to be mixin input' |> $this->message(...);
    }
}
