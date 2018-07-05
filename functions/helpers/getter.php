<?php

use Bitrix\Highloadblock\DataManager;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\SystemException;
use Itgro\Bitrix\Catalog\PriceType;
use Itgro\Bitrix\HighloadIBlock\HighloadIblock;
use Itgro\Bitrix\IBlock\Iblock;
use Itgro\Bitrix\IBlock\Property;
use Itgro\Bitrix\IBlock\PropertyEnum;
use Itgro\Bitrix\IBlock\Section;
use Itgro\Bitrix\Module;
use Itgro\Bitrix\Sale\DeliveryService;
use Itgro\Bitrix\Sale\OrderProperty;
use Itgro\Bitrix\Sale\PaySystem;
use Itgro\Router;

if (!function_exists('get_iblock_id')) {
    /**
     * ID ИБ по коду
     *
     * @param string $code
     * @return int
     * @throws LoaderException
     */
    function get_iblock_id($code)
    {
        return Iblock::getByCode($code);
    }
}

if (!function_exists('get_property_id')) {
    /**
     * ID свойства по его коду и, если хочется, по коду/ID ИБ
     *
     * @param string $propertyCode
     * @param string|null $iBlockCode
     * @return int
     * @throws LoaderException
     */
    function get_property_id($propertyCode, $iBlockCode = null)
    {
        return Property::getByCode($propertyCode, $iBlockCode);
    }
}

if (!function_exists('get_property_enum_id')) {
    /**
     * ID элемента из списка свойства ИБ
     *
     * @param string $code
     * @param string|null $property
     * @param string|null $iBlock
     * @return int
     */
    function get_property_enum_id($code, $property = null, $iBlock = null)
    {
        return PropertyEnum::getByXmlId($code, $property, $iBlock);
    }
}

if (!function_exists('get_section_id')) {
    /**
     * ID раздела ИБ
     *
     * @param string $code
     * @param string|null $iBlock
     * @return int
     */
    function get_section_id($code, $iBlock = null)
    {
        return Section::getByCode($code, $iBlock);
    }
}

if (!function_exists('get_highload_iblock_id')) {
    /**
     * ID Highload-ИБ по имени
     *
     * @param string $name
     * @return int
     * @throws LoaderException
     */
    function get_highload_iblock_id($name)
    {
        return HighloadIblock::getByName($name);
    }
}

if (!function_exists('get_price_type_id')) {
    /**
     * ID типа цен
     *
     * @param string $code
     * @return int
     * @throws LoaderException
     */
    function get_price_type_id($code)
    {
        return PriceType::getByCode($code);
    }
}

if (!function_exists('get_delivery_service_id')) {
    /**
     * ID службы доставки
     *
     * @param string $code
     * @return int
     * @throws LoaderException
     */
    function get_delivery_service_id($code)
    {
        return DeliveryService::getByCode($code);
    }
}

if (!function_exists('get_order_property_id')) {
    /**
     * ID свойства заказа
     *
     * @param string $code
     * @return int
     * @throws LoaderException
     */
    function get_order_property_id($code)
    {
        return OrderProperty::getByCode($code);
    }
}

if (!function_exists('get_pay_system_id')) {
    /**
     * ID платёжной системы
     *
     * @param string $code
     * @return int
     * @throws LoaderException
     */
    function get_pay_system_id($code)
    {
        return PaySystem::getByCode($code);
    }
}

if (!function_exists('get_highload_iblock_entity')) {
    /**
     * Экземпляр ORM-сущности некоего Highload-ИБ
     *
     * @param string $name
     * @return DataManager
     * @throws LoaderException
     * @throws SystemException
     * @throws Exception
     */
    function get_highload_iblock_entity($name): DataManager
    {
        check_modules('highloadblock');

        $block = HighloadBlockTable::getRow([
            'filter' => ['=NAME' => $name],
        ]);

        if (empty($block)) {
            return null;
        }

        $entity = HighloadBlockTable::compileEntity($block);
        $entity = $entity->getDataClass();

        return new $entity();
    }
}

if (!function_exists('check_modules')) {
    /**
     * Проверка модулей
     * Если $throwable - true, выкинется исключение с ошибками. Иначе - вернётся массив с ними же
     *
     * @param string|array $modules
     * @param bool $throwable
     * @return array
     * @throws Exception
     * @throws LoaderException
     */
    function check_modules($modules, $throwable = true)
    {
        // Замнкутный круг - чтобы использовать соответствующий метод класса модуля, нужно проверить сначала модуль
        if (!Loader::includeModule('extensions')) {
            return [];
        }

        return Module::check($modules, $throwable);
    }
}

if (!function_exists('route')) {
    /**
     * Ссылку по его коду
     *
     * @param string $code
     * @param array $replaces
     * @return string|null
     */
    function route($code, $replaces = [])
    {
        return Router::getByCode($code, $replaces);
    }
}
