<?php

declare(strict_types=1);

namespace Mpietrucha\PHPStan;

use Mpietrucha\Support\Filesystem;

/**
 * @internal
 */
abstract class GenerateIdeHelpers
{
    public static function due(): bool
    {
        $facades = storage_path('app/framework/cache') |> Filesystem::snapshot(...);

        if ($facades === null) {
            return false;
        }

        return Cache::dirty($facades);
    }
}
