<?php

namespace Itgro\Social\Handler;

class Facebook extends Base
{
    public function icon_code()
    {
        return 'facebook';
    }

    public function domain()
    {
        return 'https://www.facebook.com/sharer.php';
    }

    public function parameters()
    {
        return [
            'src' => 'sp',
            'u' => $this->url,
            't' => $this->title,
            'description' => $this->description,
            'picture' => $this->image,
        ];
    }
}
