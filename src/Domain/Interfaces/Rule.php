<?php

declare(strict_types=1);

namespace Zolta\Domain\Interfaces;

interface Rule
{
    /**
     * Validate the value or throw InvalidArgumentException.
     * This is a guard-style rule (assert).
     *
     * @throws \InvalidArgumentException
     */
    public function validate(mixed $value): void;
}
