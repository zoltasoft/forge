<?php

namespace Zolta\Domain\ValueObjects;

use Zolta\Domain\Attributes\UseRule;
use Zolta\Domain\Interfaces\VO;
use Zolta\Domain\Rules\MaxLengthRule;
use Zolta\Domain\Rules\NonEmptyRule;

final class RoleName extends ValueObject
{
    protected array $getters = ['value'];

    public function __construct(
        #[UseRule(NonEmptyRule::class)]
        #[UseRule(MaxLengthRule::class, ['max' => 50])]
        protected string $value,

    ) {
        parent::__construct();
    }

    public function equals(VO $other): bool
    {
        return $other instanceof self && $this->value === $other->value;
    }
}
