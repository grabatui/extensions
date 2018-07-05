<?php

namespace Itgro\Ajax\Result;

class Result
{
    protected $type;
    protected $value;
    protected $additional;

    public function __construct($type, $value = null, $additional = [])
    {
        $this->type = $type;
        $this->value = $value;
        $this->additional = $additional;
    }

    public function toArray()
    {
        return array_merge(
            $this->additional,
            [
                'type' => $this->type,
                'value' => $this->value,
            ]
        );
    }
}
