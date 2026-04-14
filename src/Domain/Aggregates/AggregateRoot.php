<?php

declare(strict_types=1);

namespace Zolta\Domain\Aggregates;

use Zolta\Domain\Entities\Entity;

/**
 * Base class for all aggregate roots.
 *
 * Extends Entity to provide domain event lifecycle
 * and unified access semantics.
 */
abstract class AggregateRoot extends Entity
{
    // Currently inherits everything from Entity.
    // You can later add invariant enforcement helpers or reconstitution logic here.
}
