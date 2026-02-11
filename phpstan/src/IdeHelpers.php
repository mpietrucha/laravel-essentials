<?php

namespace Mpietrucha\PHPStan;

use Illuminate\Support\Facades\Artisan;
use Mpietrucha\PHPStan\Bootstrap\Action;
use Mpietrucha\PHPStan\Bootstrap\Cache;
use Mpietrucha\Utility\Filesystem;
use Mpietrucha\Utility\Type;

/**
 * @internal
 */
abstract class IdeHelpers extends Action
{
    public static function due(): bool
    {
        $facades = storage_path('app/framework/cache') |> Filesystem::snapshot(...);

        if (Type::null($facades)) {
            return false;
        }

        return Cache::dirty($facades);
    }

    protected static function handle(): void
    {
        Artisan::call('ide:helpers');
    }
}
