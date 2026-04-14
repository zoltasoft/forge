<?php

declare(strict_types=1);

namespace Zolta\Domain\Specifications;

use DateTimeImmutable;

final readonly class MinAgeSpecification
{
    public function __construct(private int $minAge) {}

    public function isSatisfiedBy(\DateTimeInterface $dob): bool
    {
        $now = new DateTimeImmutable;

        return $now->diff(DateTimeImmutable::createFromInterface($dob))->y >= $this->minAge;
    }
}
