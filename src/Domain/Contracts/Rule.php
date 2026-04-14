<?php

declare(strict_types=1);

namespace Zolta\Domain\Contracts;

abstract class Rule implements RuleInterface
{
    public function apply(mixed $value, array $options = []): mixed
    {
        $this->validate($value, $options);

        return $value;
    }

    public function validate(mixed $value, array $options = []): void
    {
        // default no-op
    }

    /**
     * Compose sequential rules: left then right.
     */
    public function and(RuleInterface $rule): RuleInterface
    {
        $a = $this;

        return new class($a, $rule) extends Rule implements RuleInterface
        {
            public function __construct(private readonly RuleInterface $a, private readonly RuleInterface $b) {}

            public function apply(mixed $value, array $options = []): mixed
            {
                $value = $this->a->apply($value, $options);

                return $this->b->apply($value, $options);
            }

            public function validate(mixed $value, array $options = []): void
            {
                $this->a->validate($value, $options);
                $this->b->validate($value, $options);
            }
        };
    }

    /**
     * Logical OR for rules: if first fails, try second.
     */
    public function or(RuleInterface $rule): RuleInterface
    {
        $a = $this;

        return new class($a, $rule) extends Rule implements RuleInterface
        {
            public function __construct(private readonly RuleInterface $a, private readonly RuleInterface $b) {}

            public function apply(mixed $value, array $options = []): mixed
            {
                try {
                    return $this->a->apply($value, $options);
                } catch (\Throwable $e1) {
                    try {
                        return $this->b->apply($value, $options);
                    } catch (\Throwable $e2) {
                        // combine messages
                        throw new \DomainException($e1->getMessage().' OR '.$e2->getMessage(), $e1->getCode(), $e1);
                    }
                }
            }

            public function validate(mixed $value, array $options = []): void
            {
                $errs = [];
                try {
                    $this->a->validate($value, $options);

                    return;
                } catch (\Throwable $e) {
                    $errs[] = $e->getMessage();
                }
                try {
                    $this->b->validate($value, $options);

                    return;
                } catch (\Throwable $e) {
                    $errs[] = $e->getMessage();
                }
                throw new \DomainException(implode(' OR ', $errs));
            }
        };
    }
}
