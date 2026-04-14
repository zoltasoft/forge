<?php

namespace Zolta\Domain\ValueObjects;

/**
 * Terms enum (VO)
 */
enum Terms: string
{
    case accepted = 'accepted';
    case declined = 'declined';

    public function equals(self $other): bool
    {
        return $this === $other;
    }
}
