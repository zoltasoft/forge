<?php

declare(strict_types=1);

namespace Zolta\Exceptions\Rest;

class UnauthorizedException extends RestApiException
{
    protected int $statusCode = 401;

    protected function exceptionMessage(): string
    {
        return 'Unauthorized.';
    }
}
