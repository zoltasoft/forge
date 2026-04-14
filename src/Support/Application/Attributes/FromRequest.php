<?php

declare(strict_types=1);

namespace Zolta\Support\Application\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
class FromRequest
{
    public function __construct(
        public ?string $field = null,       // request key, defaults to param name
        public ?string $transformer = null  // optional transformer class/method
    ) {}
}
