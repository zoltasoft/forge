<?php

declare(strict_types=1);

namespace Zolta\Domain\Contracts;

interface InvariantInterface
{
    /**
     * Ensure invariant for VO. $options allows reuse/config.
     *
     * @param  array<string,mixed>  $options
     */
    public function ensure(\Zolta\Domain\Interfaces\VO $vo, array $options = []): void;
}
