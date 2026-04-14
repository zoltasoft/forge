<?php

declare(strict_types=1);

namespace Zolta\Domain\Rules;

use InvalidArgumentException;
use Zolta\Domain\Contracts\Rule;

final class PositiveNumberRule extends Rule
{
    public function apply(mixed $value, array $options = []): mixed
    {
        if (! is_numeric($value) || $value < 0) {
            throw new InvalidArgumentException("$value must be a non-negative number");
        }

        return $value;
    }
}
