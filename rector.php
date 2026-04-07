<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\ArrowFunction\StaticArrowFunctionRector;
use Rector\CodingStyle\Rector\Closure\StaticClosureRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector;
use Rector\DeadCode\Rector\Expression\RemoveDeadStmtRector;
use Rector\DeadCode\Rector\Node\RemoveNonExistingVarAnnotationRector;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use RectorLaravel\Rector\FuncCall\RemoveRedundantValueCallsRector;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withPaths([
        'src',
        'config',
        'analyze',
        'phpstan',
    ])
    ->withSkip([
        RemoveDeadStmtRector::class,
        ClosureToArrowFunctionRector::class,
        RemoveNonExistingVarAnnotationRector::class,
        RemoveUselessParamTagRector::class,
        RemoveRedundantValueCallsRector::class => [
            'src/Eloquent/Models/Concerns/HasConvertedPrice.php',
            'src/Eloquent/Models/Concerns/HasMoney.php',
        ],
    ])
    ->withRules([
        StaticClosureRector::class,
        StaticArrowFunctionRector::class,
    ])
    ->withSets([
        LaravelSetList::LARAVEL_130,
        LaravelSetList::LARAVEL_CODE_QUALITY,
        LaravelSetList::LARAVEL_TYPE_DECLARATIONS,
        LaravelSetList::LARAVEL_ELOQUENT_MAGIC_METHOD_TO_QUERY_BUILDER,
        LaravelSetList::LARAVEL_IF_HELPERS,
        LaravelSetList::LARAVEL_COLLECTION,
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        typeDeclarationDocblocks: true,
        privatization: true,
        naming: true,
        instanceOf: true,
        earlyReturn: true,
        carbon: true
    )
    ->withPhpSets(php85: true)
    ->withBootstrapFiles([
        'phpstan/bootstrap.php',
    ])
    ->withPhpstanConfigs([
        'phpstan.neon',
    ]);
