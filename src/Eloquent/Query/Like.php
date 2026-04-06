<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Eloquent\Query;

abstract class Like
{
    public static function start(string $search): string
    {
        return sprintf('%%%s', $search);
    }

    public static function finish(string $search): string
    {
        return sprintf('%s%%', $search);
    }

    public static function wrap(string $search): string
    {
        return sprintf('%%%s%%', $search);
    }
}
