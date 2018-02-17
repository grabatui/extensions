<?php (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Config\Option;

class AdminModuleComponent extends CBitrixComponent
{
    private $module;

    /** @var CAdminTabControl */
    private $tabControl;

    public function __construct($component)
    {
        parent::__construct($component);
    }

    public function onPrepareComponentParams($arParams)
    {
        $this->module = array_get($arParams, 'MODULE_ID');

        return $arParams;
    }

    private function saveOptions()
    {
        foreach ($this->arResult['tabs'] as $tab) {
            foreach ($tab['fields'] as $fieldCode => $field) {
                Option::set($this->module, $fieldCode, $this->request->getPost($fieldCode));
            }
        }
    }

    private function setTabs()
    {
        $tabs = [];
        foreach (array_get($this->arParams, 'TABS', []) as $tabCode => $tabProperties) {
            $tabs[] = [
                'DIV' => $tabCode,
                'TAB' => array_get($tabProperties, 'tab_title', array_get($tabProperties, 'title')),
                'ICON' => '',
                'TITLE' => array_get($tabProperties, 'title'),
            ];
        }

        $this->tabControl = new CAdminTabControl(sprintf('%s_tabs', $this->module), $tabs);

        $this->arResult['tab_control'] = $this->tabControl;

        $this->arResult['tabs'] = array_filter(array_get($this->arParams, 'TABS', []), function ($tab) {
            $fields = array_get($tab, 'fields', []);

            return (is_array($fields) && !empty($fields));
        });
    }

    private function setOptions()
    {
        foreach ($this->arResult['tabs'] as &$tab) {
            foreach ($tab['fields'] as $fieldCode => &$field) {
                $field['value'] = Option::get($this->module, $fieldCode, '');
            }
            unset($field);
        }
        unset($tab);
    }

    public function executeComponent()
    {
        $this->setTabs();

        if ($this->request->isPost()) {
            $this->saveOptions();
        }

        $this->setOptions();

        $this->tabControl->Begin();

        $this->includeComponentTemplate();

        $this->tabControl->End();
    }
}