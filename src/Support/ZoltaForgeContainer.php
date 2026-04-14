<?php

declare(strict_types=1);

namespace Zolta\Support;

use Psr\Container\ContainerInterface;
use RuntimeException;

/**
 * Framework-neutral wrapper that exposes only PSR-11 semantics.
 */
final readonly class ZoltaForgeContainer implements ContainerInterface
{
    public function __construct(private ContainerInterface $container) {}

    public function get(string $id): mixed
    {
        if ($this->container->has($id)) {
            return $this->container->get($id);
        }

        if (method_exists($this->container, 'hasParameter') && $this->container->hasParameter($id)) {
            /** @phpstan-ignore-next-line */
            return $this->container->getParameter($id);
        }

        return $this->container->get($id);
    }

    public function has(string $id): bool
    {
        if ($this->container->has($id)) {
            return true;
        }

        return method_exists($this->container, 'hasParameter') && $this->container->hasParameter($id);
    }

    /**
     * Prevent calls to anything outside PSR-11.
     *
     * @param  array<mixed>  $arguments
     *
     * @throws RuntimeException always
     */
    public function __call(string $method, array $arguments): never
    {
        throw new RuntimeException("Unsupported method {$method}. ZoltaForgeContainer exposes only PSR-11 get()/has().");
    }
}
