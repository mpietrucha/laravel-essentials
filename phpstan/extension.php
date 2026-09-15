<?php

declare(strict_types=1);

use Mpietrucha\PHPStan\Command\ErrorFormatter\MixinErrorFormatter;
use Mpietrucha\PHPStan\File\PHPStanCacheFileFinder;
use Mpietrucha\PHPStan\Methods\IconExtension;
use Mpietrucha\PHPStan\Methods\MacroExtension;
use Mpietrucha\PHPStan\ReturnTypes\FacadeExtension;
use Mpietrucha\PHPStan\Types\NumericGreaterThanZeroExtension;
use Mpietrucha\Support\Filesystem\Path;
use Mpietrucha\Support\Number;

return [
    'services' => [
        '00' => [
            'class' => MacroExtension::class,
            'tags' => ['phpstan.broker.methodsClassReflectionExtension'],
        ],
        [
            'class' => IconExtension::class,
            'tags' => ['phpstan.broker.methodsClassReflectionExtension'],
        ],
        [
            'class' => FacadeExtension::class,
            'tags' => ['phpstan.broker.dynamicStaticMethodReturnTypeExtension'],
        ],
        [
            'class' => NumericGreaterThanZeroExtension::class,
            'arguments' => [
                'class' => Number::class,
            ],
            'tags' => ['phpstan.typeSpecifier.staticMethodTypeSpecifyingExtension'],
        ],
        'errorFormatter.mixin' => [
            'class' => MixinErrorFormatter::class,
            'arguments' => ['@errorFormatter.table'],
        ],
        'fileFinderAnalyse' => [
            'class' => PHPStanCacheFileFinder::class,
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
                'path' => PHPStanCacheFileFinder::getCacheDirectory(),
            ],
        ],
        'scanDirectories' => [
            PHPStanCacheFileFinder::getCacheDirectory(),
        ],
        'bootstrapFiles' => [
            Path::build('bootstrap.php', __DIR__),
        ],
    ],
];
