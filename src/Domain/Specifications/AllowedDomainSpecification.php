<?php

declare(strict_types=1);

namespace Zolta\Domain\Specifications;

use Zolta\Domain\Contracts\Specification;

final class AllowedDomainSpecification extends Specification
{
    public function isSatisfiedBy(mixed $candidate, array $options = []): bool
    {
        $allowed = $options['allowed'] ?? null;
        if ($allowed === null) {
            // if no allowed list provided, treat as pass
            return true;
        }
        $addr = (string) $candidate;
        $pos = strrpos($addr, '@');
        if ($pos === false) {
            return false;
        }
        $domain = substr($addr, $pos + 1);

        return in_array($domain, (array) $allowed, true);
    }

    public function message(): string
    {
        return 'Email domain is not allowed';
    }
}
