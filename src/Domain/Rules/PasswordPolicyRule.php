<?php

declare(strict_types=1);

namespace Zolta\Domain\Rules;

use DomainException;
use Zolta\Domain\Contracts\Rule;

final class PasswordPolicyRule extends Rule
{
    private readonly int $minLength;

    /**
     * @param  array<string, mixed>  $options
     */
    public function __construct(array $options = [])
    {
        $this->minLength = $options['minLength'] ?? 8;
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public function validate(mixed $value, array $options = []): void
    {
        $min = $options['minLength'] ?? $this->minLength;
        if (! is_string($value) || strlen($value) < $min) {
            throw new DomainException("Password must be at least {$min} characters long");
        }
    }
}
