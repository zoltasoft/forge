<?php

declare(strict_types=1);

namespace Zolta\Domain\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class UsePolicy
{
    /**
     * @param  array<string, mixed>  $options
     */
    public function __construct(
        public string $policyClass,
        public array $options = []
    ) {}
}
