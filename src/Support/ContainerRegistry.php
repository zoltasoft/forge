<?php

declare(strict_types=1);

namespace Zolta\Support;

use Psr\Container\ContainerInterface;
use RuntimeException;
use Zolta\Framework\FrameworkRegistry;

/**
 * Central registry for the framework-agnostic container.
 */
final class ContainerRegistry
{
    private static ?ZoltaForgeContainer $zoltaForgeContainer = null;

    /**
     * Set the container (wraps in strict wrapper automatically).
     */
    public static function set(ContainerInterface $container): void
    {
        self::$zoltaForgeContainer = new ZoltaForgeContainer($container);
    }

    /**
     * Get the registered container.
     *
     * @throws RuntimeException if no container was registered
     */
    public static function get(): ZoltaForgeContainer
    {
        if (! self::$zoltaForgeContainer instanceof ZoltaForgeContainer) {
            throw new RuntimeException('Zolta container has not been registered.');
        }

        return self::$zoltaForgeContainer;
    }

    /**
     * Resolve an object from the registered container.
     *
     * @template T
     *
     * @param  class-string<T>  $object
     * @return T
     *
     * @throws RuntimeException
     */
    public static function resolve(string $object): mixed
    {
        if (! self::$zoltaForgeContainer instanceof ZoltaForgeContainer) {
            throw new RuntimeException('Zolta container has not been registered.');
        }

        $implementation = FrameworkRegistry::resolveBinding($object);

        if (! is_string($implementation) || $implementation === '') {
            throw new RuntimeException(sprintf(
                'No framework binding found for [%s].',
                $object
            ));
        }

        try {
            return self::$zoltaForgeContainer->get($implementation);
        } catch (\Throwable $e) {
            throw new RuntimeException(sprintf(
                'Failed to resolve [%s] (mapped to [%s]).',
                $object,
                $implementation
            ), $e->getCode(), previous: $e);
        }
    }
}
