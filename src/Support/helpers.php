<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Psr\Log\LoggerInterface;
use Zolta\Support\ContainerRegistry;
use Zolta\Support\ZoltaForgeContainer;

if (! function_exists('app') && ! class_exists(Application::class)) {
    /**
     * Resolve a service from the global container.
     *
     * The helper exposes only the PSR-11 contract, so calling framework-specific
     * helpers (`make`, `bind`, etc.) is impossible and any attempt will throw.
     *
     * @template T of object
     *
     * @param  class-string<T>|null  $id
     * @return T|ZoltaForgeContainer
     *
     * @throws RuntimeException if the container has not been registered
     */
    function app(?string $id = null): mixed
    {
        $zoltaForgeContainer = ContainerRegistry::get();

        if ($id === null) {
            return $zoltaForgeContainer;
        }

        return $zoltaForgeContainer->get($id);
    }
}

if (! function_exists('logger')) {
    /**
     * Resolve a PSR logger from the container if available.
     */
    function logger(): ?LoggerInterface
    {
        return zoltaLogger();
    }
}

if (! function_exists('zoltaLogger')) {
    /**
     * Resolve a PSR logger from the container if available (framework-agnostic).
     */
    function zoltaLogger(): ?LoggerInterface
    {
        try {
            $container = ContainerRegistry::get();
            foreach ([LoggerInterface::class, 'logger'] as $id) {
                try {
                    if ($container->has($id)) {
                        $logger = $container->get($id);
                        if ($logger instanceof LoggerInterface) {
                            return $logger;
                        }
                    }
                } catch (Throwable) {
                    // ignore and continue to next id
                }
            }
        } catch (Throwable) {
            // ignore
        }

        return null;
    }
}
