<?php

declare(strict_types=1);

use Contao\Rector\Set\ContaoSetList;
use Rector\CodingStyle\Rector\FuncCall\FunctionFirstClassCallableRector;
use Rector\Config\RectorConfig;
use Rector\Php81\Rector\Array_\ArrayToFirstClassCallableRector;
use Rector\Php84\Rector\Param\ExplicitNullableParamTypeRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\TypeDeclaration\Rector\ClassLike\AddVoidReturnTypeWhereNoReturnRector;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/contao',
        __DIR__ . '/tests',
        __DIR__ . '/translations',
        __DIR__ . '/rector.php',
        __DIR__ . '/ecs.php',
    ])
    ->withPhpVersion(PhpVersion::PHP_82)
    ->withRules([
        AddVoidReturnTypeWhereNoReturnRector::class,
        // In Vorbereitung für PHP 8.4:
        ExplicitNullableParamTypeRector::class,
    ])

    ->withImportNames(
        importShortClasses: false,
        removeUnusedImports: true
    )
    ->withComposerBased(
        twig: true,
        doctrine: true,
        phpunit: true,
        symfony: true,
    )
    ->withSets([
        LevelSetList::UP_TO_PHP_82,
        ContaoSetList::CONTAO_50,
        ContaoSetList::CONTAO_51,
        ContaoSetList::CONTAO_53,
        ContaoSetList::FQCN,
        ContaoSetList::ANNOTATIONS_TO_ATTRIBUTES,
    ])
    ->withSkip([
        ArrayToFirstClassCallableRector::class,
        FunctionFirstClassCallableRector::class,
    ])
;
