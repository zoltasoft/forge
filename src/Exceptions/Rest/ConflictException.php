<?php

declare(strict_types=1);

namespace Zolta\Exceptions\Rest;

class ConflictException extends RestApiException
{
    protected int $statusCode = 409;

    protected function exceptionMessage(): string
    {
        return 'Conflict occurred.';
    }
}
