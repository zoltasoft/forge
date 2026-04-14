<?php

declare(strict_types=1);

namespace Zolta\Framework;

/**
 * Tracks framework adapters and resolves compatible bindings for the runtime.
 */
final class FrameworkRegistry
{
    /**
     * @var array<class-string<FrameworkAdapterInterface>>
     */
    private static array $adapters = [];

    /**
     * @var array<class-string<FrameworkAdapterInterface>>|null
     */
    private static ?array $resolvedAdapters = null;

    /**
     * @var array<string, string|null>
     */
    private static array $bindingCache = [];

    /**
     * Register a framework adapter for consideration.
     */
    public static function register(string $adapter): void
    {
        if (! is_a($adapter, FrameworkAdapterInterface::class, true)) {
            return;
        }

        if (in_array($adapter, self::$adapters, true)) {
            return;
        }

        self::$adapters[] = $adapter;

        // Invalidate caches
        self::$resolvedAdapters = null;
        self::$bindingCache = [];
    }

    /**
     * Resolve all adapters that support the current runtime,
     * ordered by priority (highest first).
     *
     * @return array<class-string<FrameworkAdapterInterface>>
     */
    private static function resolveAdapters(): array
    {
        if (self::$resolvedAdapters !== null) {
            return self::$resolvedAdapters;
        }

        FrameworkBootstrap::boot();

        $candidates = [];

        foreach (self::$adapters as $adapter) {
            if (! class_exists($adapter)) {
                continue;
            }

            if (! $adapter::supports()) {
                continue;
            }

            $candidates[$adapter] = $adapter::priority();
        }

        if ($candidates === []) {
            return self::$resolvedAdapters = [];
        }

        arsort($candidates, SORT_NUMERIC);

        return self::$resolvedAdapters = array_keys($candidates);
    }

    /**
     * Resolve the concrete implementation for a Core abstraction.
     *
     * The first adapter (by priority) that provides the binding wins.
     */
    public static function resolveBinding(string $abstraction): ?string
    {
        if (array_key_exists($abstraction, self::$bindingCache)) {
            return self::$bindingCache[$abstraction];
        }

        foreach (self::resolveAdapters() as $adapter) {
            $bindings = $adapter::bindings();

            if (! is_array($bindings)) {
                continue;
            }

            if (isset($bindings[$abstraction])) {
                return self::$bindingCache[$abstraction] = $bindings[$abstraction];
            }
        }

        return self::$bindingCache[$abstraction] = null;
    }

    /**
     * OPTIONAL (BC helper):
     * Returns the highest-priority adapter for the runtime.
     * Useful if you still need "the framework adapter" concept.
     */
    public static function resolve(): ?string
    {
        $adapters = self::resolveAdapters();

        return $adapters[0] ?? null;
    }
}
