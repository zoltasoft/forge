<?php

declare(strict_types=1);

namespace Zolta\Domain\Contracts;

interface SpecificationInterface
{
    /**
     * @param  array<string,mixed>  $options
     */
    public function isSatisfiedBy(mixed $candidate, array $options = []): bool;

    public function message(): string;
}
