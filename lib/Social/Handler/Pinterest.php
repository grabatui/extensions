<?php

namespace Itgro\Social\Handler;

class Pinterest extends Base
{
    public function icon_code()
    {
        return 'pencil';
    }

    public function domain()
    {
        return 'https://pinterest.com/pin/create/button/';
    }

    public function parameters()
    {
        return [
            'url' => $this->url,
            'media' => $this->image,
            'description' => $this->title,
        ];
    }
}
