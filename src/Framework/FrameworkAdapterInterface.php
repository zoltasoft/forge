<?php

declare(strict_types=1);

namespace Zolta\Framework;

/**
 * Describes a framework adapter that can supply bindings for Core abstractions.
 */
interface FrameworkAdapterInterface
{
    /**
     * Detect whether this adapter supports the current runtime without side effects.
     */
    public static function supports(): bool;

    /**
     * Adapter priority when multiple adapters match (higher value wins).
     */
    public static function priority(): int;

    /**
     * Return framework bindings for Core abstractions.
     *
     * @return array<class-string, class-string>
     */
    public static function bindings(): array;
}
