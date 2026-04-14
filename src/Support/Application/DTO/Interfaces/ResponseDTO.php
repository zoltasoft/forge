<?php

declare(strict_types=1);

namespace Zolta\Support\Application\DTO\Interfaces;

/**
 * Marker interface for response/output DTOs.
 */
interface ResponseDTO
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
