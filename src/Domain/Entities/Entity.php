<?php

declare(strict_types=1);

namespace Zolta\Domain\Entities;

use InvalidArgumentException;
use Zolta\Domain\Entities\Interfaces\Entity as EntityInterface;
use Zolta\Domain\Events\Contracts\EventInterface;
use Zolta\Domain\Serialization\Traits\Normalizable;

/**
 * Base class for all domain entities.
 *
 * Provides:
 *  - Safe read-only accessors for domain properties
 *  - Optional domain event recording (for future aggregate promotion)
 *  - Normalization support for serialization
 */
abstract class Entity implements EntityInterface
{
    use Normalizable;

    /** @var EventInterface[] */
    protected array $recordedEvents = [];

    /**
     * Record a domain event.
     */
    protected function recordThat(EventInterface $event): void
    {
        $this->recordedEvents[] = $event;
    }

    /**
     * Release and clear recorded domain events.
     *
     * @return EventInterface[]
     */
    public function releaseEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];

        return $events;
    }

    // --------------------------------------
    // Magic Accessor Layer (read-only)
    // --------------------------------------

    public function __get(string $name): mixed
    {
        $getter = 'get'.ucfirst($name);

        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        if (property_exists($this, $name)) {
            return $this->$name;
        }

        throw new InvalidArgumentException("Property '{$name}' not accessible on ".static::class);
    }

    public function __set(string $name, mixed $value): void
    {
        throw new \LogicException("Cannot modify property '{$name}' on Entity ".static::class);
    }

    public function __isset(string $name): bool
    {
        return method_exists($this, 'get'.ucfirst($name)) || property_exists($this, $name);
    }
}
