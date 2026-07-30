<?php

declare(strict_types=1);

namespace Mpietrucha\PHPStan;

use Mpietrucha\Support\Filesystem;

use function Orchestra\Testbench\uses_default_skeleton;

/**
 * @internal
 */
abstract class GenerateIdeHelpers
{
    public static function due(): bool
    {
        if (uses_default_skeleton()) {
            return false;
        }

        $app = app_path() |> Filesystem::snapshot(...);

        if ($app === null) {
            return false;
        }

        return Cache::dirty($app);
    }
}
