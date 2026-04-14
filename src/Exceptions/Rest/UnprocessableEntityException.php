<?php

declare(strict_types=1);

namespace Zolta\Exceptions\Rest;

class UnprocessableEntityException extends RestApiException
{
    protected int $statusCode = 422;

    protected function exceptionMessage(): string
    {
        return 'Unprocessable entity.';
    }
}
