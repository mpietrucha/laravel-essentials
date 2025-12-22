<?php

namespace Mpietrucha\Laravel\Package\Macro\Exception;

use Mpietrucha\Utility\Throwable\InvalidArgumentException;

class MacroException extends InvalidArgumentException
{
    public function initialize(): void
    {
        'Macro destination must use the Macroable trait' |> $this->message(...);
    }
}
