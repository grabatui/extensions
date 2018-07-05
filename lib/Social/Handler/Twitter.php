<?php

namespace Itgro\Social\Handler;

class Twitter extends Base
{
    public function icon_code()
    {
        return 'twitter';
    }

    public function domain()
    {
        return 'https://twitter.com/intent/tweet';
    }

    public function parameters()
    {
        return [
            'text' => $this->title,
            'url' => $this->url,
        ];
    }
}
