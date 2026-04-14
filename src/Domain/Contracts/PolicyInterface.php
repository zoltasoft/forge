<?php

declare(strict_types=1);

namespace Zolta\Domain\Contracts;

interface PolicyInterface
{
    /**
     * Apply policy on VO (post-construction).
     *
     * @param  array<string,mixed>  $options
     */
    public function apply(\Zolta\Domain\Interfaces\VO $vo, array $options = []): mixed;
}
