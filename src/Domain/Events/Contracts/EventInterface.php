<?php

namespace Zolta\Domain\Events\Contracts;

interface EventInterface
{
    public function occurredOn(): \DateTimeImmutable;
}
