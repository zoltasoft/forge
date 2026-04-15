<?php

declare(strict_types=1);

namespace Zolta\Domain\Contracts;

use Zolta\Domain\Interfaces\VO;

interface InvariantInterface
{
    /**
     * Ensure invariant for VO. $options allows reuse/config.
     *
     * @param  array<string,mixed>  $options
     */
    public function ensure(VO $vo, array $options = []): void;
}
