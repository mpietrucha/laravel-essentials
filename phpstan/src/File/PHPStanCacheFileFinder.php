<?php

declare(strict_types=1);

namespace Mpietrucha\PHPStan\File;

use Mpietrucha\Support\Filesystem;
use Mpietrucha\Support\Filesystem\Path;
use PHPStan\File\DirectoryWalker;
use PHPStan\File\FileExcluder;
use PHPStan\File\FileFinder as PHPStanFileFinder;
use PHPStan\File\FileFinderResult;
use PHPStan\File\FileHelper;

/**
 * @internal
 *
 * @phpstan-type Paths array<string>
 */
final class PHPStanCacheFileFinder
{
    private static ?string $cacheDirectory = null;

    private readonly PHPStanFileFinder $phpStanFileFinder;

    /**
     * @param  array<string>  $fileExtensions
     */
    public function __construct(FileExcluder $fileExcluder, FileHelper $fileHelper, array $fileExtensions, DirectoryWalker $directoryWalker)
    {
        /** @phpstan-ignore phpstanApi.constructor */
        $this->phpStanFileFinder = new PHPStanFileFinder($fileExcluder, $fileHelper, $fileExtensions, $directoryWalker);
    }

    public static function getCacheDirectory(): string
    {
        if ($cacheDirectory = self::$cacheDirectory) {
            return $cacheDirectory;
        }

        $phpstanDirectory = Path::directory(__DIR__, 2);

        Filesystem::ensureDirectoryExists($cacheDirectory = Path::join($phpstanDirectory, 'cache'));

        return self::$cacheDirectory = $cacheDirectory;
    }

    /**
     * @param  Paths  $paths
     */
    public function findFiles(array $paths): FileFinderResult
    {
        /** @phpstan-ignore phpstanApi.method */
        return $this->appendCacheDirectory($paths) |> $this->phpStanFileFinder->findFiles(...);
    }

    /**
     * @param  Paths  $paths
     */
    public function findFilesCached(array $paths): FileFinderResult
    {
        /** @phpstan-ignore phpstanApi.method */
        return $this->appendCacheDirectory($paths) |> $this->phpStanFileFinder->findFilesCached(...);
    }

    /**
     * @param  Paths  $paths
     * @return Paths
     */
    private function appendCacheDirectory(array $paths): array
    {
        $paths[] = self::getCacheDirectory();

        return $paths;
    }
}
