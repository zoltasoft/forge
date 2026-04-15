<?php

namespace Zolta\Domain\ValueObjects;

use Zolta\Domain\Attributes\UseRule;
use Zolta\Domain\Interfaces\VO;
use Zolta\Domain\Rules\NonEmptyRule;
use Zolta\Domain\Rules\PasswordPolicyRule;

final class Password extends ValueObject
{
    protected array $getters = ['hash'];

    public function __construct(
        #[UseRule(NonEmptyRule::class)]
        #[UseRule(PasswordPolicyRule::class, ['minLength' => 8])]
        protected string $hash,
        protected ?VOConstructionContext $context = null
    ) {
        parent::__construct();
    }

    public static function fromHashed(string $hashed, ?VOConstructionContext $context = null): static
    {
        return new self($hashed, $context);
    }

    public function equals(VO $other): bool
    {
        return $other instanceof self && $this->hash === $other->get('hash');
    }
}
