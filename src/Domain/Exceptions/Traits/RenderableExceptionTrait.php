<?php

declare(strict_types=1);

namespace Zolta\Domain\Exceptions\Traits;

use ReflectionClass;

trait RenderableExceptionTrait
{
    public function type(): string
    {
        return (new ReflectionClass($this))->getShortName();
    }

    public function context(): array
    {
        return [];
    }

    public function toErrorArray(): array
    {
        return [
            'type' => $this->type(),
            'message' => $this->getMessage(),
            'context' => $this->context(),
        ];
    }
}
