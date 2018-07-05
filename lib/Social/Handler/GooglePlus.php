<?php

namespace Itgro\Social\Handler;

class GooglePlus extends Base
{
    public function icon_code()
    {
        return 'google-plus';
    }

    public function domain()
    {
        return 'https://plus.google.com/share';
    }

    public function parameters()
    {
        return [
            'url' => $this->url,
        ];
    }
}
