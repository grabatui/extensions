<?php

namespace Itgro\Ajax\Result;

use Bitrix\Main\Entity\Result;

class BitrixError extends Error
{
    public function __construct(Result $value, array $additional = [])
    {
        parent::__construct(implode(', ', $value->getErrorMessages()), $additional);
    }
}
