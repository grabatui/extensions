<?php

namespace Itgro\Ajax\Result;

class Error extends Result
{
    public function __construct($value = null, array $additional = [])
    {
        parent::__construct('error', $value, $additional);
    }
}
