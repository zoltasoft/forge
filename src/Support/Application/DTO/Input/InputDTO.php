<?php

declare(strict_types=1);

namespace Zolta\Support\Application\DTO\Input;

use Zolta\Support\Application\DTO\Interfaces\DTO;
use Zolta\Support\Application\DTO\Interfaces\InputDTO as InputDTOInterface;
use Zolta\Support\Traits\Normalizable;

abstract class InputDTO implements DTO, InputDTOInterface
{
    use Normalizable;
}
