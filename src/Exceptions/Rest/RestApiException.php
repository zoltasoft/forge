<?php

declare(strict_types=1);

namespace Zolta\Exceptions\Rest;

use Zolta\Exceptions\BaseException;

abstract class RestApiException extends BaseException
{
    protected int $statusCode = 400;

    public function status(): int
    {
        return $this->statusCode;
    }
}
