<?php

declare(strict_types=1);

namespace Zolta\Exceptions\Rest;

class ForbiddenException extends RestApiException
{
    protected int $statusCode = 403;

    protected function exceptionMessage(): string
    {
        return 'Forbidden.';
    }
}
