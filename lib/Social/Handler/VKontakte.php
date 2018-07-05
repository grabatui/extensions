<?php

namespace Itgro\Social\Handler;

class VKontakte extends Base
{
    public function icon_code()
    {
        return 'vk';
    }

    public function domain()
    {
        return 'https://vkontakte.ru/share.php';
    }

    public function parameters()
    {
        return [
            'url' => $this->url,
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
        ];
    }
}
