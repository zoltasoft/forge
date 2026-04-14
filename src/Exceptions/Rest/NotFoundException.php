<?php

declare(strict_types=1);

namespace Zolta\Exceptions\Rest;

use Throwable;

class NotFoundException extends RestApiException
{
    protected int $statusCode = 404;

    public function __construct(
        private readonly ?string $customMessage = null,
        ?Throwable $previous = null,
        ?string $errorCode = null,
        array $context = []
    ) {
        parent::__construct($previous, $errorCode, $context, $this->statusCode);
    }

    protected function exceptionMessage(): string
    {
        return $this->customMessage ?? 'Resource not found.';
    }
}
