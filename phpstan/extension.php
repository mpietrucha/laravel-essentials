<?php

use Mpietrucha\PHPStan\Command\ErrorFormatter\MixinErrorFormatter;
use Mpietrucha\PHPStan\File\FileFinder;
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
            'class' => FileFinder::class,
            'arguments' => [
                'fileExcluder' => '@fileExcluderAnalyse',
                'fileExtensions' => '%fileExtensions%',
            ],
            'autowired' => false,
        ],
    ],
    'parameters' => [
        'errorFormat' => 'mixin',
        'scanDirectories' => [
            FileFinder::cacheDirectory(),
        ],
        'bootstrapFiles' => [
            Path::build('bootstrap.php', __DIR__),
        ],
    ],
];
