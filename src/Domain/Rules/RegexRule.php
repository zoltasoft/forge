<?php

declare(strict_types=1);

namespace Zolta\Domain\Rules;

use InvalidArgumentException;
use Zolta\Domain\Contracts\Rule;

/**
 * Generic regex-based validation rule.
 */
final class RegexRule extends Rule
{
    public function __construct(
        private readonly string $pattern,
        private readonly ?string $fieldName = null
    ) {}

    public function validate(mixed $value, array $options = []): void
    {
        if (! is_string($value)) {
            throw new InvalidArgumentException('Value must be a string for regex validation.');
        }

        if (@preg_match($this->pattern, '') === false) {
            throw new InvalidArgumentException("Invalid regex pattern: {$this->pattern}");
        }

        if (preg_match($this->pattern, $value) !== 1) {
            $label = $this->fieldName ?? 'value';

            throw new InvalidArgumentException("{$label} does not match required format.");
        }
    }
}
