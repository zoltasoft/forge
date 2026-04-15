<?php

declare(strict_types=1);

namespace Zolta\Domain\Contracts;

use Zolta\Domain\Interfaces\VO;

interface PolicyInterface
{
    /**
     * Apply policy on VO (post-construction).
     *
     * @param  array<string,mixed>  $options
     */
    public function apply(VO $vo, array $options = []): mixed;
}
