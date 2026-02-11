<?php

namespace Mpietrucha\PHPStan;

use Illuminate\Support\Facades\Artisan;
use Mpietrucha\Laravel\Essentials\Mixin;
use Mpietrucha\PHPStan\Bootstrap\Action;
use Mpietrucha\PHPStan\Bootstrap\Cache;

/**
 * @internal
 */
abstract class MixinAnalyzers extends Action
{
    public static function due(): bool
    {
        $mixins = Mixin::map();

        if ($mixins->isEmpty()) {
            return false;
        }

        return $mixins->hash() |> Cache::dirty(...);
    }

    protected static function handle(): void
    {
        Artisan::call('mixin:analyzers');
    }
}
