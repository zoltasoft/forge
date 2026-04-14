<?php

declare(strict_types=1);

namespace Zolta\Exceptions\Contracts;

interface RenderableExceptionInterface
{
    /**
     * Structured representation used by infrastructure to render responses.
     *
     * Example:
     * [
     *   'type' => 'ValidationException',
     *   'message' => 'Validation failed',
     *   'context' => ['errors' => [...]],
     * ]
     *
     * @return array<string, mixed>
     */
    public function toErrorArray(): array;

    /**
     * Logical / HTTP status (domain may return logical code; infra maps to HTTP).
     */
    public function status(): int;

    /**
     * Short machine-friendly type name (e.g. ValidationException).
     */
    public function type(): string;

    /**
     * Optional structured context for logs/clients (default empty).
     *
     * @return array<string, mixed>
     */
    public function context(): array;
}
