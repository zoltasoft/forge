<?php

declare(strict_types=1);

namespace Zolta\Domain\Interfaces;

interface Policy
{
    /** Policy-specific behavior; signature is intentionally generic. */
    public function decide(mixed $context): mixed;
}
