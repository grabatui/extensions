<?php

namespace Itgro\Social\Handler;

class MailRu extends Base
{
    public function icon_code()
    {
        return 'at';
    }

    public function domain()
    {
        return 'https://connect.mail.ru/share';
    }

    public function parameters()
    {
        return [
            'url' => $this->url,
            'title' => $this->title,
            'description' => $this->description,
        ];
    }
}
