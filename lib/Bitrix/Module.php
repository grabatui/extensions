<?php

namespace Itgro\Bitrix;

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Exception;

class Module
{
    const NOT_LOADED_MODULE_MESSAGE_TEMPLATE = 'Ошибка при подключении модуля "%s"';

    /**
     * @param string|array $modules
     * @param bool $throwable
     * @return array
     * @throws Exception
     * @throws LoaderException
     */
    public static function check($modules, $throwable = true)
    {
        if (is_string($modules)) {
            $modules = [$modules];
        }

        if (!is_array($modules)) {
            return [];
        }

        $errors = [];
        foreach ($modules as $module) {
            if (!Loader::includeModule($module)) {
                 $errors[] = sprintf(self::NOT_LOADED_MODULE_MESSAGE_TEMPLATE, $module);
            }
        }

        if (!empty($errors) && $throwable) {
            throw new Exception(implode(', ', $errors));
        }

        return $errors;
    }
}
