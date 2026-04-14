<?php

declare(strict_types=1);

namespace Zolta\Domain\Interfaces;

interface Serializer
{
    /**
     * @return array<string, mixed>
     */
    public function serialize(mixed $data): array;
}
