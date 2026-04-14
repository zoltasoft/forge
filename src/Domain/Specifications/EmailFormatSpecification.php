<?php

declare(strict_types=1);

namespace Zolta\Domain\Specifications;

use Zolta\Domain\Contracts\Specification;

final class EmailFormatSpecification extends Specification
{
    public function isSatisfiedBy(mixed $candidate, array $options = []): bool
    {
        return filter_var((string) $candidate, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function message(): string
    {
        return 'Invalid email format';
    }
}
