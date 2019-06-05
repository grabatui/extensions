<?php

namespace Itgro\Bitrix\Admin;

use CJSCore;
use Itgro\Bitrix\Admin\Button\Base;

trait WithAdditionalExtensions
{
    public function addElementAdminButton(Base $button)
    {
        $this->executeForElementWithCheck(function () use ($button) {
            $button->render();
        });
    }

    public function addElementsListAdminButton(Base $button)
    {
        $this->executeForElementsListWithCheck(function () use ($button) {
            $button->render();
        });
    }

    public function addElementPropertyInformation($propertyCode, $jsFunction)
    {
        $propertyId = (!is_numeric($propertyCode)) ? get_property_id($propertyCode, $this->iBlockCode) : $propertyCode;

        $this->executeForElementWithCheck(function () use ($propertyId, $jsFunction) {
            $jsFunction = (is_callable($jsFunction)) ? call_user_func($jsFunction, $propertyId, $this) : $jsFunction;

            if (!is_string($jsFunction)) {
                return;
            }
            ?>

            <script type="application/javascript">
                $(document).ready(function () {
                    var propertyRow = $('tr#tr_PROPERTY_<?= $propertyId; ?>');

                    if (propertyRow && propertyRow.length > 0) {
                        <?= $jsFunction; ?>();
                    }
                });
            </script>

            <?php
        });
    }

    private function executeForElementWithCheck($callback)
    {
        if (!$this->isAdminPage() || !$this->isScriptLike('iblock_element_edit.php')) {
            return;
        }

        $this->addPrologueEvent($callback);
    }

    private function executeForElementsListWithCheck($callback)
    {
        if (
            !$this->isAdminPage() ||
            (!$this->isScriptLike('iblock_list_admin.php') && !$this->isScriptLike('iblock_element_admin.php'))
        ) {
            return;
        }

        $this->addPrologueEvent($callback);
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

    private function isAdminPage()
    {
        return (defined('ADMIN_SECTION') && ADMIN_SECTION);
    }

    private function isScriptLike($script)
    {
        return (strpos(request()->getScriptFile(), $script) !== false);
    }
}
