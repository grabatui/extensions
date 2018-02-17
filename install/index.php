<?php

use Bitrix\Main\ModuleManager;

class extensions extends CModule
{
    protected $events = [
        '' => [
            'onAfterTwigTemplateEngineInited' => [
                ['\Itgro\Twig\Extension', 'expand'],
            ],
        ],
    ];

    public function __construct()
    {
        $composer = $this->getComposerInfo();

        $this->MODULE_ID = 'extensions';
        $this->MODULE_VERSION = (array_key_exists('version', $composer)) ? $composer['version'] : '';
        $this->MODULE_VERSION_DATE = (array_key_exists('version_date', $composer)) ? $composer['version_date'] : '';

        $this->MODULE_NAME = 'Помощник Айтигро';
        $this->MODULE_DESCRIPTION = '';

        $this->PARTNER_NAME = '';
        $this->PARTNER_URI = '';
    }

    public function DoInstall()
    {
        $this->InstallDB();
        $this->InstallEvents();
        $this->InstallFiles();

        ModuleManager::registerModule($this->MODULE_ID);
    }

    public function DoUninstall()
    {
        ModuleManager::unRegisterModule($this->MODULE_ID);

        $this->UnInstallFiles();
        $this->UnInstallEvents();
        $this->UnInstallDB();
    }

    public function InstallEvents()
    {
        $this->switchEvents(true);
    }

    public function UnInstallEvents()
    {
        $this->switchEvents(false);
    }

    public function InstallFiles()
    {
        // TODO: Продумать как заполнять список дистрибьюторов
        // TODO: Перенести остальные классы и проверить уже перенесенные

        CopyDirFiles(__DIR__ . '/components', $_SERVER['DOCUMENT_ROOT'] . '/local/components');
        CopyDirFiles(__DIR__ . '/ajax', $_SERVER['DOCUMENT_ROOT'] . '/ajax');
    }

    private function getComposerInfo()
    {
        try {
            return json_decode(file_get_contents(realpath(__DIR__ . '/../composer.json')), true);
        } catch (Throwable $exception) {
            return [];
        }
    }

    private function switchEvents($install = true)
    {
        foreach ($this->events as $moduleName => $moduleEvents) {
            foreach ($moduleEvents as $eventName => $eventClasses) {
                foreach ($eventClasses as $eventClass) {
                    list($class, $method) = $eventClass;

                    ($install) ?
                        event_manager()->registerEventHandler($moduleName, $eventName, $this->MODULE_ID, $class, $method) :
                        event_manager()->unRegisterEventHandler($moduleName, $eventName, $this->MODULE_ID, $class, $method);
                }
            }
        }
    }
}
