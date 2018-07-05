<?php

namespace Itgro\Social\Handler;

class Odnoklassniki extends Base
{
    public function icon_code()
    {
        return 'odnoklassniki';
    }

    public function domain()
    {
        return 'https://connect.ok.ru/dk';
    }

    public function parameters()
    {
        return [
            'st.cmd' => 'WidgetSharePreview',
            'st.title' => $this->title,
            'st.description' => $this->description,
            'st.imageUrl' => $this->image,
            'st.shareUrl' => $this->url,
        ];
    }
}
