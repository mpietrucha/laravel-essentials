<?php

declare(strict_types=1);

use Mpietrucha\PHPStan\Command\ErrorFormatter\MixinErrorFormatter;
use Mpietrucha\PHPStan\File\CacheFileFinder;
use Mpietrucha\PHPStan\Methods\MacroExtension;
use Mpietrucha\PHPStan\ReturnTypes\FacadeExtension;
use Mpietrucha\Support\Filesystem\Path;

return [
    'services' => [
        '00' => [
            'class' => MacroExtension::class,
            'tags' => ['phpstan.broker.methodsClassReflectionExtension'],
        ],
        [
            'class' => FacadeExtension::class,
            'tags' => ['phpstan.broker.dynamicStaticMethodReturnTypeExtension'],
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
