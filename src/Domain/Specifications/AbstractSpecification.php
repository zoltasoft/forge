<?php

declare(strict_types=1);

namespace Zolta\Domain\Specifications;

use Zolta\Domain\Contracts\Specification as SpecificationContract;

abstract class AbstractSpecification extends SpecificationContract
{
    abstract public function isSatisfiedBy(mixed $candidate, array $options = []): bool;

    public function message(): string
    {
        return 'unspecified rule';
    }
}
