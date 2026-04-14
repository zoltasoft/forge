<?php

declare(strict_types=1);

namespace Zolta\Domain\Interfaces;

use Zolta\Domain\ValueObjects\VOConstructionContext;

interface VO
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function resolve(
        array $data,
        ?VOConstructionContext $voConstructionContext = null
    ): static;

    /**
     * Convert the Value Object into an array representation.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;

    /**
     * Determine whether two Value Objects are equal.
     */
    public function equals(VO $vo): bool;
}
