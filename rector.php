<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictNativeCallRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;

return static function (RectorConfig $rectorConfig): void {

    // ----------------------------------------------
    // 1) Target directories
    // ----------------------------------------------
    $rectorConfig->paths([
        __DIR__.'/src',
    ]);

    // ----------------------------------------------
    // 2) Areas Rector must never touch
    // ----------------------------------------------
    $rectorConfig->skip([
        __DIR__.'/vendor',

        // VO system relies on manual constructors & factories
        __DIR__.'/src/Domain/ValueObjects',

        // Domain events resolved by reflection
        __DIR__.'/src/Domain/Events',

        // Tests must never be refactored
        __DIR__.'/tests',
    ]);

    // ----------------------------------------------
    // 2b) Runtime configuration
    // ----------------------------------------------
    $rectorConfig->disableParallel();

    // ----------------------------------------------
    // 3) Base upgrade sets
    // ----------------------------------------------
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_82,
        SetList::TYPE_DECLARATION,
        SetList::NAMING,
        SetList::PRIVATIZATION,
        SetList::CODE_QUALITY,      // safe, improves code readability
    ]);

    // ----------------------------------------------
    // 4) Extra strict rules — safe for Zolta Forge
    // ----------------------------------------------
    $rectorConfig->rules([
        // Adds return types only when 100% certain
        ReturnTypeFromStrictNativeCallRector::class,

        // Convert constructor assignments → typed properties
        TypedPropertyFromStrictConstructorRector::class,

        // Add strict_types=1
        DeclareStrictTypesRector::class,
    ]);
};
