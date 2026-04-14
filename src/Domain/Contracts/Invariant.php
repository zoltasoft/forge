<?php

declare(strict_types=1);

namespace Zolta\Domain\Contracts;

use DomainException;
use InvalidArgumentException;
use Zolta\Domain\Interfaces\VO;

abstract class Invariant implements InvariantInterface
{
    public function ensure(VO $vo, array $options = []): void
    {
        // default no-op
    }

    protected function assertVOType(VO $vo, string $expectedClass): void
    {
        if (! $vo instanceof $expectedClass) {
            throw new InvalidArgumentException(sprintf('Expected VO of type %s, got %s', $expectedClass, $vo::class));
        }
    }

    protected function throwIf(bool $condition, string $message): void
    {
        if ($condition) {
            throw new DomainException($message);
        }
    }

    public function and(InvariantInterface $invariant): InvariantInterface
    {
        $self = $this;

        return new class($self, $invariant) extends Invariant implements InvariantInterface
        {
            public function __construct(private readonly InvariantInterface $a, private readonly InvariantInterface $b) {}

            public function ensure(VO $vo, array $options = []): void
            {
                $this->a->ensure($vo, $options);
                $this->b->ensure($vo, $options);
            }
        };
    }

    public function or(InvariantInterface $invariant): InvariantInterface
    {
        $self = $this;

        return new class($self, $invariant) extends Invariant implements InvariantInterface
        {
            public function __construct(private readonly InvariantInterface $a, private readonly InvariantInterface $b) {}

            public function ensure(VO $vo, array $options = []): void
            {
                try {
                    $this->a->ensure($vo, $options);

                    return;
                } catch (\Throwable $e1) {
                    try {
                        $this->b->ensure($vo, $options);

                        return;
                    } catch (\Throwable $e2) {
                        throw new DomainException($e1->getMessage().' OR '.$e2->getMessage(), $e1->getCode(), $e1);
                    }
                }
            }
        };
    }
}
