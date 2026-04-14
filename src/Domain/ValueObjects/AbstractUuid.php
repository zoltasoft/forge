<?php

namespace Zolta\Domain\ValueObjects;

use Ramsey\Uuid\Uuid;
use Zolta\Domain\Attributes\UseRule;
use Zolta\Domain\Rules\UuidRule;

/**
 * Thin UUID holder. Validation is expected to be done by pipeline via UuidSpecification.
 *
 * @phpstan-consistent-constructor
 */
abstract class AbstractUuid extends ValueObject
{
    #[UseRule(UuidRule::class)]
    protected readonly ?string $value;

    public function __construct(?string $value = null, ?VOConstructionContext $context = null)
    {
        if ($value === null) {
            $value = Uuid::uuid4()->toString();
        }
        $resolved = self::resolveInternal(['value' => $value], $context);
        $this->value = $resolved['value'];
    }

    public function toString(): string
    {
        return (string) $this->value;
    }

    public static function fromString(string $value): static
    {
        return new static($value);
    }

    public static function default(): static
    {
        $class = static::class;
        /** @var static $instance */
        $instance = new $class(Uuid::uuid4()->toString());

        return $instance;
    }
}
