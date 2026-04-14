<?php

declare(strict_types=1);

namespace Zolta\Domain\Interfaces;

interface Specification
{
    /** Returns true when the candidate satisfies the specification */
    public function isSatisfiedBy(mixed $candidate): bool;

    /** Optional human message for diagnostics */
    public function message(): string;
}
