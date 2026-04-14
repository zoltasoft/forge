<?php

declare(strict_types=1);

namespace Zolta\Domain\Services;

use Ramsey\Uuid\Uuid;

final class UuidDefaultProvider
{
    public function get(): string
    {
        return Uuid::uuid4()->toString();
    }
}
