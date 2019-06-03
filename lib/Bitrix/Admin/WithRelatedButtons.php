<?php

namespace Itgro\Bitrix\Admin;

use CJSCore;
use Itgro\Bitrix\Admin\Button\Base;

trait WithRelatedButtons
{
    public function addAdminButton(Base $button)
    {
        if (
            !defined('ADMIN_SECTION') ||
            !ADMIN_SECTION ||
            strpos(request()->getScriptFile(), 'iblock_element_edit.php') === false
        ) {
            return;
        }

        $this->addPrologueEvent(function () use ($button) {
            $button->render();
        });
    }

    private function addPrologueEvent($callback)
    {
        event_manager()->addEventHandler('main', 'OnProlog', function () use ($callback) {
            if (get_iblock_id($this->getIBlockCode()) != request()->getQuery('IBLOCK_ID') || !CJSCore::Init('jquery')) {
                return;
            }

            call_user_func($callback);
        });
    }
}
