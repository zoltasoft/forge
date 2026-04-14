<?php

declare(strict_types=1);

namespace Zolta\Support\Application\DTO\Output;

use Zolta\Support\Application\DTO\Interfaces\DTO;
use Zolta\Support\Application\DTO\Interfaces\ResponseDTO as ResponseDTOInterface;
use Zolta\Support\Traits\Normalizable;

abstract class ResponseDTO implements DTO, ResponseDTOInterface
{
    use Normalizable;
}
