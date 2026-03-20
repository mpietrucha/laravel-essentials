<?php

declare(strict_types=1);

namespace Mpietrucha\PHPStan\File;

use Mpietrucha\Support\Filesystem\Path;
use PHPStan\File\FileExcluder;
use PHPStan\File\FileFinder;
use PHPStan\File\FileFinderResult;
use PHPStan\File\FileHelper;

/**
 * @internal
 */
final class MixinFileFinder
{
    private FileFinder $fileFinder;

    /**
     * @param  array<string>  $fileExtensions
     */
    public function __construct(FileExcluder $fileExcluder, FileHelper $fileHelper, array $fileExtensions)
    {
        /** @phpstan-ignore phpstanApi.constructor */
        $this->fileFinder = new FileFinder($fileExcluder, $fileHelper, $fileExtensions);
    }

    /**
     * @param  array<string>  $paths
     */
    public function findFiles(array $paths): FileFinderResult
    {
        $cache = Path::build('../../cache', __DIR__);

        $paths[] = $cache;

        /** @phpstan-ignore phpstanApi.method */
        return $this->fileFinder->findFiles($paths);
    }
}
