<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector;
use Rector\DeadCode\Rector\Expression\RemoveDeadStmtRector;
use Rector\DeadCode\Rector\Node\RemoveNonExistingVarAnnotationRector;
use Rector\Php55\Rector\FuncCall\GetCalledClassToSelfClassRector;
use Rector\Php55\Rector\FuncCall\GetCalledClassToStaticClassRector;
use Rector\Php70\Rector\Ternary\TernaryToNullCoalescingRector;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php80\Rector\FuncCall\ClassOnObjectRector;
use Rector\Php85\Rector\Property\AddOverrideAttributeToOverriddenPropertiesRector;
use RectorLaravel\Rector\Class_\DescriptionPropertyToDescriptionAttributeRector;
use RectorLaravel\Rector\Class_\FillablePropertyToFillableAttributeRector;
use RectorLaravel\Rector\Class_\HiddenPropertyToHiddenAttributeRector;
use RectorLaravel\Rector\Class_\SignaturePropertyToSignatureAttributeRector;
use RectorLaravel\Rector\Class_\TablePropertyToTableAttributeRector;
use RectorLaravel\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector;
use RectorLaravel\Rector\If_\ThrowIfRector;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withPaths([
        'src',
        'config',
        'analyze',
        'phpstan',
        'database',
    ])
    ->withSkip([
        'phpstan/cache',
        'src/Eloquent/Casts/Attribute.php',
        RemoveDeadStmtRector::class,
        ClosureToArrowFunctionRector::class,
        RemoveNonExistingVarAnnotationRector::class,
        RemoveUselessParamTagRector::class,
        AddGenericReturnTypeToRelationsRector::class,
        FillablePropertyToFillableAttributeRector::class,
        TablePropertyToTableAttributeRector::class,
        HiddenPropertyToHiddenAttributeRector::class,
        GetCalledClassToStaticClassRector::class,
        GetCalledClassToSelfClassRector::class,
        ClassOnObjectRector::class,
        AddOverrideAttributeToOverriddenPropertiesRector::class,
        SignaturePropertyToSignatureAttributeRector::class,
        DescriptionPropertyToDescriptionAttributeRector::class,
        TernaryToNullCoalescingRector::class => [
            'src/Macro.php',
        ],
        ThrowIfRector::class => [
            'src/Money/Exchanger/FrankfurterExchangerService.php',
        ],
    ])
    ->withSets([
        LaravelSetList::LARAVEL_130,
        LaravelSetList::LARAVEL_CODE_QUALITY,
        LaravelSetList::LARAVEL_TYPE_DECLARATIONS,
        LaravelSetList::LARAVEL_ELOQUENT_MAGIC_METHOD_TO_QUERY_BUILDER,
        LaravelSetList::LARAVEL_IF_HELPERS,
        LaravelSetList::LARAVEL_COLLECTION,
        LaravelSetList::LARAVEL_ARRAYACCESS_TO_METHOD_CALL,
        LaravelSetList::LARAVEL_ARRAY_STR_FUNCTION_TO_STATIC_CALL,
        LaravelSetList::LARAVEL_CONTAINER_STRING_TO_FULLY_QUALIFIED_NAME,
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        typeDeclarationDocblocks: true,
        privatization: true,
        carbon: true
    )
    ->withPhpSets(php85: true)
    ->withBootstrapFiles([
        'phpstan/bootstrap.php',
    ])
    ->withPhpstanConfigs([
        'phpstan.neon',
    ]);
