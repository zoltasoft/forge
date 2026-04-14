<?php

declare(strict_types=1);

namespace Zolta\Support\Application\DTO\Interfaces;

/**
 * Marker interface for input DTOs (commands / queries / request payloads).
 *
 * @method array<string, mixed> toArray()
 */
interface InputDTO
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
