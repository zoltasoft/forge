<?php

namespace Zolta\Domain\ValueObjects;

use Zolta\Domain\Attributes\UseRule;
use Zolta\Domain\Interfaces\VO;
use Zolta\Domain\Rules\MaxLengthRule;
use Zolta\Domain\Rules\NonEmptyRule;

final class PermissionName extends ValueObject
{
    protected array $getters = ['value'];

    #[UseRule(NonEmptyRule::class)]
    #[UseRule(MaxLengthRule::class, ['max' => 100])]
    protected string $value;

    public function __construct(string $value, ?VOConstructionContext $context = null)
    {
        $resolved = self::resolveInternal(['value' => $value], $context);
        $this->value = $resolved['value'];
    }

    public function equals(VO $other): bool
    {
        return $other instanceof self && $this->value === $other->value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
