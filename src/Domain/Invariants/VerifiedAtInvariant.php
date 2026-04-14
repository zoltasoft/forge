<?php

declare(strict_types=1);

namespace Zolta\Domain\Invariants;

use DateTimeImmutable;
use DomainException;
use Zolta\Domain\Contracts\Invariant;
use Zolta\Domain\Interfaces\VO;

final class VerifiedAtInvariant extends Invariant
{
    public function ensure(VO $vo, array $options = []): void
    {
        // if ($dt === null) return;
        // if ($dt > new DateTimeImmutable()) {
        //     throw new DomainException("$param cannot be in the future");
        // }
    }
}
