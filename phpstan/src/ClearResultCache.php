<?php

declare(strict_types=1);

namespace Mpietrucha\PHPStan;

use Symfony\Component\Console\Input\ArgvInput;

/**
 * @internal
 */
abstract class ClearResultCache
{
    public static function due(): bool
    {
        return new ArgvInput()->getFirstArgument() === 'clear-result-cache';
    }
}
