<?php

namespace Mpietrucha\Laravel\Package\Translations\Exception;

use Mpietrucha\Utility\Throwable\RuntimeException;

class TranslationPackageException extends RuntimeException
{
    public function initialize(): void
    {
        'Cannot determine translation package name' |> $this->message(...);
    }
}
