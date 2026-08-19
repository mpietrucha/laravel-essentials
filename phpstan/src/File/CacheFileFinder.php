<?php

declare(strict_types=1);

namespace Mpietrucha\PHPStan\File;

use Mpietrucha\Support\Filesystem;
use Mpietrucha\Support\Filesystem\Path;
use PHPStan\File\FileExcluder;
use PHPStan\File\FileFinder as PHPStanFileFinder;
use PHPStan\File\FileFinderResult;
use PHPStan\File\FileHelper;

/**
 * @internal
 */
final class CacheFileFinder
{
    private static ?string $cacheDirectory = null;

    private readonly PHPStanFileFinder $phpStanFileFinder;

    /**
     * @param  array<string>  $fileExtensions
     */
    public function __construct(FileExcluder $fileExcluder, FileHelper $fileHelper, array $fileExtensions)
    {
        /** @phpstan-ignore phpstanApi.constructor */
        $this->phpStanFileFinder = new PHPStanFileFinder($fileExcluder, $fileHelper, $fileExtensions);
    }

    public static function cacheDirectory(): string
    {
        if ($cacheDirectory = self::$cacheDirectory) {
            return $cacheDirectory;
        }

        $phpstanDirectory = Path::directory(__DIR__, 2);

        return self::$cacheDirectory ??= Path::join($phpstanDirectory, 'cache');
    }

    /**
     * @param  array<string>  $paths
     */
    public function findFiles(array $paths): FileFinderResult
    {
        $cacheDirectory = self::cacheDirectory();

        if (Filesystem::isDirectory($cacheDirectory)) {
            $paths[] = $cacheDirectory;
        }

        /** @phpstan-ignore phpstanApi.method */
        return $this->phpStanFileFinder->findFiles($paths);
    }
}
