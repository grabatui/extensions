<?php

namespace Itgro\Ajax\Result;

class Success extends Result
{
    public function __construct($value = null, array $additional = [])
    {
        parent::__construct('success', $value, $additional);
    }
}
