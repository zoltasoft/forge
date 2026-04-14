<?php

declare(strict_types=1);

namespace Zolta\Domain\Contracts;

use Zolta\Domain\Interfaces\VO;

abstract class Policy implements PolicyInterface
{
    public function apply(VO $vo, array $options = []): mixed
    {
        // default no-op
        return null;
    }

    /**
     * Compose policies: run both.
     */
    public function and(PolicyInterface $policy): PolicyInterface
    {
        $a = $this;

        return new class($a, $policy) extends Policy implements PolicyInterface
        {
            public function __construct(private readonly PolicyInterface $a, private readonly PolicyInterface $b) {}

            public function apply(VO $vo, array $options = []): mixed
            {
                $this->a->apply($vo, $options);

                return $this->b->apply($vo, $options);
            }
        };
    }
}
