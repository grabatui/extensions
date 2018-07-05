<?php

namespace Itgro\Social\Handler;

/**
 * @property string $title
 * @property string $description
 * @property string|null $url
 * @property array|string|null $image
 */
abstract class Base
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function __get($name)
    {
        return array_get($this->data, $name);
    }

    public function link()
    {
        return sprintf(
            '%s?%s',
            $this->domain(),
            http_build_query($this->parameters())
        );
    }

    abstract public function icon_code();

    abstract public function domain();

    abstract public function parameters();
}
