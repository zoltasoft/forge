<?php

declare(strict_types=1);

namespace Zolta\Domain\Contracts;

interface RuleInterface
{
    /**
     * Apply the rule to a single value.
     *
     * @param  array<string,mixed>  $options
     * @return mixed transformed or original value
     *
     * @throws \InvalidArgumentException|\DomainException on failure
     */
    public function apply(mixed $value, array $options = []): mixed;

    /**
     * Validate only (no transform).
     *
     * @param  array<string,mixed>  $options
     */
    public function validate(mixed $value, array $options = []): void;
}
