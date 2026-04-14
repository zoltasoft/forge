<?php

declare(strict_types=1);

namespace Zolta\Domain\Exceptions;

use Zolta\Domain\Exceptions\Contracts\RenderableExceptionInterface;
use Zolta\Domain\Exceptions\Traits\RenderableExceptionTrait;

final class ValidationException extends \Exception implements RenderableExceptionInterface
{
    use RenderableExceptionTrait;

    /**
     * Accepts both associative (field => messages) and list-based error payloads.
     *
     * @param  array<string, mixed>|array<int, array<string, mixed>>  $errors
     */
    public function __construct(private array $errors)
    {
        parent::__construct('Unprocessable Content', 422);
    }

    /**
     * Structured validation errors.
     *
     * @return array<string, mixed>|array<int, array<string, mixed>>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function toErrorArray(): array
    {
        return [
            'type' => $this->type(),
            'message' => $this->getMessage(),
            'context' => [
                'errors' => $this->errors,
            ],
        ];
    }

    public function status(): int
    {
        return 422;
    }
}
