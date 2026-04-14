<?php

declare(strict_types=1);

namespace Zolta\Domain\Interfaces;

interface Invariant
{
    /**
     * Ensure the invariant holds for the subject.
     * Throws DomainException when broken.
     */
    public function ensure(mixed $subject): void;
}
