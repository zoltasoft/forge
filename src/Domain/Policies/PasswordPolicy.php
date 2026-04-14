<?php

declare(strict_types=1);

namespace Zolta\Domain\Policies;

use InvalidArgumentException;

final readonly class PasswordPolicy
{
    public function __construct(private int $minLength = 8) {}

    /**
     * Validate & return hashed password.
     * Called by the pipeline (processWithHandler).
     */
    public function apply(string $plain): string
    {
        if ($plain === '') {
            throw new InvalidArgumentException('Password cannot be empty');
        }
        if (mb_strlen($plain) < $this->minLength) {
            throw new InvalidArgumentException("Password must be at least {$this->minLength} characters");
        }

        // additional checks (complexity) can be here
        return password_hash($plain, PASSWORD_BCRYPT);
    }

    public function verify(string $plain, string $hash): bool
    {
        return password_verify($plain, $hash);
    }
}
