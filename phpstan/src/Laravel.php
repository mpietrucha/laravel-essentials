<?php

declare(strict_types=1);

namespace Mpietrucha\PHPStan;

use Mpietrucha\Support\Filesystem;
use Mpietrucha\Support\Filesystem\Path;

/**
 * @internal
 */
abstract class Laravel
{
    public static function due(): bool
    {
        return defined('LARAVEL_START') === false;
    }

    public static function bootstrap(): void
    {
        if (! static::due()) {
            return;
        }

        Path::build('vendor/larastan/larastan/bootstrap.php') |> Filesystem::requireOnce(...);
    }
}
