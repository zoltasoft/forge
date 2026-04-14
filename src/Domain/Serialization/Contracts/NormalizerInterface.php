<?php

declare(strict_types=1);

namespace Zolta\Domain\Serialization\Contracts;

interface NormalizerInterface
{
    /**
     * @return array<string, mixed>
     */
    public function normalize(object $object): array;
}
