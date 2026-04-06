<?php

declare(strict_types=1);

namespace Mpietrucha\PHPStan;

use Mpietrucha\Laravel\Essentials\Macro\Mixin;

/**
 * @internal
 */
abstract class MixinAnalyzers
{
    public static function due(): bool
    {
        $mixins = Mixin::storage();

        if ($mixins->isEmpty()) {
            return false;
        }

        return $mixins->toJson() |> md5(...) |> Cache::dirty(...);
    }
}
