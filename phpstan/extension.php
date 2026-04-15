<?php

declare(strict_types=1);

use Mpietrucha\PHPStan\Command\ErrorFormatter\MixinErrorFormatter;
use Mpietrucha\PHPStan\File\CacheFileFinder;
use Mpietrucha\PHPStan\Methods\MacroExtension;
use Mpietrucha\Support\Filesystem\Path;

return [
    'services' => [
        [
            'class' => MacroExtension::class,
            'tags' => ['phpstan.broker.methodsClassReflectionExtension'],
        ],
        'errorFormatter.mixin' => [
            'class' => MixinErrorFormatter::class,
            'arguments' => ['@errorFormatter.table'],
        ],
        'fileFinderAnalyse' => [
            'class' => CacheFileFinder::class,
            'arguments' => [
                'fileExcluder' => '@fileExcluderAnalyse',
                'fileExtensions' => '%fileExtensions%',
            ],
            'autowired' => false,
        ],
    ],
    'parameters' => [
        'errorFormat' => 'mixin',
        'ignoreErrors' => [
            [
                'identifier' => 'missingType.generics',
                'path' => CacheFileFinder::cacheDirectory(),
            ],
        ],
        'scanDirectories' => [
            CacheFileFinder::cacheDirectory(),
        ],
        'bootstrapFiles' => [
            Path::build('bootstrap.php', __DIR__),
        ],
    ],
];
