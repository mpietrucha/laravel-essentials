<?php

namespace Mpietrucha\Laravel\Package\Context;

use Mpietrucha\Laravel\Package\Context;
use Mpietrucha\Laravel\Package\Context\Concerns\InteractsWithCache;
use Mpietrucha\Utility\Arr;
use Mpietrucha\Utility\Filesystem;
use Mpietrucha\Utility\Filesystem\Path;
use Mpietrucha\Utility\Finder;
use Mpietrucha\Utility\Str;
use Mpietrucha\Utility\Type;
use Mpietrucha\Utility\Utilizer\Concerns\Utilizable;
use Mpietrucha\Utility\Utilizer\Contracts\UtilizableInterface;

abstract class Provider implements UtilizableInterface
{
    use InteractsWithCache, Utilizable\Strings;

    public static function name(): string
    {
        return Str::chopStart(static::utilize(), '*');
    }

    public static function pattern(): string
    {
        return Str::start(static::name(), '*');
    }

    public static function get(): ?string
    {
        $directory = Context::directory();

        if (Type::null($directory)) {
            return null;
        }

        if ($provider = static::cache()->get($directory)) {
            return $provider;
        }

        static::cache()->put($directory, $provider = static::build($directory));

        return $provider;
    }

    protected static function build(string $directory): ?string
    {
        $default = static::default($directory);

        if (Type::string($default)) {
            return $default;
        }

        $finder = static::pattern() |> Finder::create($directory)->name(...);

        return $finder->ignoreVCSIgnored(true)
            ->files()
            ->get()
            ->first();
    }

    protected static function default(string $directory, string ...$intermediates): ?string
    {
        $file = Path::build(static::name(), Path::join($directory, ...$intermediates));

        if (Filesystem::exists($file)) {
            return $file;
        }

        if (Arr::isNotEmpty($intermediates)) {
            return null;
        }

        return static::default($directory, 'Providers') ?? static::default($directory, 'src') ?? static::default($directory, 'src', 'Providers');
    }

    protected static function hydrate(): string
    {
        return 'ServiceProvider.php';
    }
}
