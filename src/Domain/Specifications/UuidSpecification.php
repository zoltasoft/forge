<?php

declare(strict_types=1);

namespace Zolta\Domain\Specifications;

use Ramsey\Uuid\Uuid;

final class UuidSpecification
{
    public function validate(mixed $value): void
    {
        if (! is_string($value) || ! Uuid::isValid($value)) {
            throw new \InvalidArgumentException('Invalid UUID: '.(string) $value);
        }
    }
}
