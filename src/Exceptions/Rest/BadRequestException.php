<?php

declare(strict_types=1);

namespace Zolta\Exceptions\Rest;

class BadRequestException extends RestApiException
{
    protected int $statusCode = 400;

    protected function exceptionMessage(): string
    {
        return 'Bad Request.';
    }
}
