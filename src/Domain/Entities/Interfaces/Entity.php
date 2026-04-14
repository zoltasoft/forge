<?php

declare(strict_types=1);

namespace Zolta\Domain\Entities\Interfaces;

/**
 * Marker interface for domain entities.
 *
 * Entities have identity and may contain domain behavior,
 * but do not necessarily act as aggregate roots.
 */
interface Entity
{
    // Intentionally empty (semantic marker)
}
