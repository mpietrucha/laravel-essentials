<?php

namespace Mpietrucha\Laravel\Package\Context;

use Mpietrucha\Utility\Backtrace\Contracts\FrameInterface;
use Mpietrucha\Utility\Normalizer;
use Mpietrucha\Utility\Str;
use Mpietrucha\Utility\Type;

abstract class Frame
{
    public const string PACKAGE = 'mpietrucha/laravel-package';

    public static function internal(FrameInterface $frame): bool
    {
        $file = $frame->file();

        if (Type::null($file)) {
            return true;
        }

        return Str::contains($file, static::PACKAGE);
    }

    final public static function external(FrameInterface $frame): bool
    {
        return static::internal($frame) |> Normalizer::not(...);
    }
}
