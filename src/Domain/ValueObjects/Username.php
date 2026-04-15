<?php

namespace Zolta\Domain\ValueObjects;

use Zolta\Domain\Attributes\UseRule;
use Zolta\Domain\Interfaces\VO;
use Zolta\Domain\Rules\MaxLengthRule;
use Zolta\Domain\Rules\NonEmptyRule;

final class Username extends ValueObject
{
    protected array $getters = ['username'];

    public function __construct(
        #[UseRule(NonEmptyRule::class)]
        #[UseRule(MaxLengthRule::class, ['max' => 100])]
        protected string $username
    ) {
        parent::__construct();
    }

    public function equals(VO $other): bool
    {
        return $other instanceof self && $this->username === $other->get('username');
    }
}
