<?php

declare(strict_types=1);

namespace Zolta\Domain\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER | Attribute::IS_REPEATABLE)]
final class UseSpecification
{
    /**
     * @param  array<string, mixed>  $options
     */
    public function __construct(
        public string $specClass,
        public array $options = []
    ) {}
}
