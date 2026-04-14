<?php

namespace Zolta\Domain\ValueObjects;

use Zolta\Domain\Attributes\UseRule;
use Zolta\Domain\Rules\MaxLengthRule;
use Zolta\Domain\Rules\NonEmptyRule;

/**
 * Represents a human-readable description.
 */
final class Description extends ValueObject
{
    protected array $getters = ['value'];

    public function __construct(
        // #[UseRule(NonEmptyRule::class)]
        #[UseRule(MaxLengthRule::class, ['max' => 100])]
        protected string $value,
        protected ?VOConstructionContext $context = null
    ) {
        parent::__construct();
    }

    public static function default(): static
    {
        return new self('No description provided.');
    }

    public function toArray(): array
    {
        return ['description' => $this->value];
    }

    /**
     * @param  array<int|string, callable|string>|string  $key
     */
    public function get(array|string $key = 'value'): mixed
    {
        if ($key === 'description') {
            return $this->value;
        }

        return parent::get($key);
    }

    public function equals(\Zolta\Domain\Interfaces\VO $other): bool
    {
        return $other instanceof self && $this->value === $other->get('value');
    }
}
