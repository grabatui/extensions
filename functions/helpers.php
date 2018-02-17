<?php

use Bitrix\Highloadblock\DataManager;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\SystemException;
use Itgro\Bitrix\HighloadIblock;
use Itgro\Bitrix\Iblock;
use Itgro\Bitrix\Module;
use Itgro\Bitrix\Property;

if (!function_exists('include_file')) {
    /**
     * Простое подключение файла
     *
     * @param string $path
     */
    function include_file($path)
    {
        include(sprintf('%s/%s', $_SERVER['DOCUMENT_ROOT'], $path));
    }
}

if (!function_exists('include_editable_file')) {
    /**
     * Подключение редактируемого файла
     *
     * @param string $path
     * @param string $mode
     * @param array $params
     * @return mixed
     */
    function include_editable_file($path, $mode = 'html', $params = [])
    {
        global $APPLICATION;

        return $APPLICATION->IncludeFile($path, [], array_merge($params, ['MODE' => $mode]));
    }
}

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
        if (!Loader::includeModule('itgro')) {
            return 0;
        }

        return Iblock::getByCode($code);
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
        if (!Loader::includeModule('itgro')) {
            return 0;
        }

        return HighloadIblock::getByName($name);
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
        if (!Loader::includeModule('itgro')) {
            return 0;
        }

        return Property::getByCode($propertyCode, $iBlockCode);
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
     */
    function get_highload_iblock_entity($name): DataManager
    {
        if (!Loader::includeModule('highloadblock')) {
            return null;
        }

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

if (!function_exists('array_get')) {
    /**
     * Элемент массива по ключу.
     * Есть возможность использовать dot-нотацию (т.е. по ключу `foo.bar` из массива `['foo' => ['bar' => 10]]` достанется 10)
     *
     * @param array $array
     * @param string $key
     * @param mixed|null $default
     * @return mixed|null
     */
    function array_get($array, $key, $default = null)
    {
        if (!is_array($array)) {
            return $default;
        }

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        $result = $array;
        foreach (explode('.', $key) as $innerKey) {
            if (is_array($result) && array_key_exists($innerKey, $result)) {
                $result = $result[$innerKey];
            } else {
                return $default;
            }
        }

        return $result;
    }
}

if (!function_exists('abort_404')) {
    /**
     * Битриксовский выброс 404ой ошибки
     *
     * @return void
     */
    function abort_404()
    {
        if (CHTTP::GetLastStatus() === 404) {
            return;
        }

        CHTTP::SetStatus('404 Not Found');

        @define('ERROR_404', 'Y');

        return;
    }
}

if (!function_exists('only_digits')) {
    /**
     * Исключительно цифры из любой строки
     *
     * @param string $string
     * @param int|null $length
     * @return string
     */
    function only_digits(string $string, $length = null): string
    {
        preg_match_all('/\d+/', $string, $matches);

        $digits = (!empty($matches)) ? implode('', $matches[0]) : '';

        if (strlen($digits) <= 0 || !is_numeric($length) || $length <= 0) {
            return $digits;
        }

        return substr($digits, 0, $length);
    }
}

if (!function_exists('convert_to_phone')) {
    /**
     * Превращение телефона в формат +7 (999) 999-99-99
     *
     * @param string $rawPhone
     * @return string
     */
    function convert_to_phone(string $rawPhone): string
    {
        $phone = only_digits($rawPhone, 10);

        if (!$phone) {
            return $rawPhone;
        }

        preg_match('/(\d{3})(\d{3})(\d{2})(\d{2})/', $phone, $matches);

        if (is_array($matches)) {
            array_shift($matches);
        }

        $matches = array_values(array_filter($matches));

        if (empty($matches) || count($matches) !== 4) {
            return $rawPhone;
        }

        return sprintf('+7 (%s) %s-%s-%s', $matches[0], $matches[1], $matches[2], $matches[3]);
    }
}

if (!function_exists('camel_to_snake')) {
    /**
     * CamelCase -> snake_case
     *
     * @param string $camel
     * @return string
     */
    function camel_to_snake(string $camel): string
    {
        preg_match_all('/([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)/', $camel, $matches);

        $matches = reset($matches);

        if (!$matches) {
            return $camel;
        }

        foreach ($matches as &$match) {
            $match = ($match === mb_strtoupper($match)) ? mb_strtolower($match) : lcfirst($match);
        }
        unset($match);

        return implode('_', $matches);
    }
}

if (!function_exists('pre')) {
    /**
     * Дебажная распечатка
     *
     * @param mixed $value
     */
    function pre($value)
    {
        echo '<pre>';
        var_dump($value);
        echo '</pre>';
    }
}

if (!function_exists('dd')) {
    /**
     * Дебажная распечатка со смертью скрипта
     *
     * @param mixed $value
     */
    function dd($value)
    {
        pre($value);
        die();
    }
}

if (!function_exists('parse_url_query')) {
    /**
     * Распрарсивает query-строку в массив (`foo=bar&baz=3` -> `['foo' => 'bar', 'baz' => '3']`)
     * Внимание! Не поддерживаются сложные элементы типа `foo[]=bar`
     *
     * @param string $url
     * @return array
     */
    function parse_url_query(string $url): array
    {
        $url = parse_url($url, PHP_URL_QUERY);

        $result = [];
        if (empty($url)) {
            return $result;
        }

        foreach (explode('&', $url) as $urlItem) {
            list($key, $value) = explode('=', $urlItem);

            $result[$key] = $value;
        }

        return $result;
    }
}

if (!function_exists('price_format')) {
    function price_format($price, $format = false): string
    {
        return sprintf('%s ₽', ($format) ? number_format($price, 0, ',', ' ') : $price);
    }
}

if (!function_exists('percent_format')) {
    function percent_format($percent, $format = false, $round = true): string
    {
        if ($format) {
            $percent = number_format($percent, 2, '.', ' ');

            if ($round) {
                $percent = (float)$percent;
            }

            $percent = str_replace('.', ',', (string)$percent);
        }

        return sprintf('%s%%', $percent);
    }
}

if (!function_exists('get_ip')) {
    function get_ip(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
}

if (!function_exists('expand_variable')) {
    /**
     * @param array|mixed $original
     * @param array|mixed $expansion
     * @return array
     */
    function expand_variable($original, $expansion)
    {
        if (!is_array($original)) {
            $original = [];
        }

        if (!is_array($expansion)) {
            $expansion = [$expansion];
        }

        return array_merge($original, $expansion);
    }
}

if (!function_exists('end_with')) {
    /**
     * @param string $string
     * @param int $length
     * @return string
     */
    function end_with_ellipsis($string, $length = null)
    {
        if (!$string) {
            return $string;
        }

        if (is_int($length) && $length > 0) {
            if (mb_strlen($string) <= $length) {
                return $string;
            }

            $string = mb_substr($string, 0, $length);
        }

        return sprintf('%s&hellip;', $string);
    }
}

if (!function_exists('get_files')) {
    /**
     * Файлы по их ID
     *
     * @param int|array $fileIds
     * @return array
     */
    function get_files($fileIds)
    {
        if (!$fileIds) {
            return [];
        }

        if (!is_array($fileIds) && is_numeric($fileIds)) {
            $fileIds = [$fileIds];
        }

        $rsFiles = CFile::GetList(
            [],
            [
                '@ID' => implode(',', array_filter($fileIds)),
                'ACTIVE' => 'Y',
            ]
        );

        $files = [];
        while ($file = $rsFiles->Fetch()) {
            $file['src'] = CFile::GetFileSRC($file);

            $files[$file['ID']] = $file;
        }

        return $files;
    }
}

if (!function_exists('get_file')) {
    /**
     * Массив файла по его ID
     *
     * @param int $id
     * @return mixed|null
     */
    function get_file($id)
    {
        $files = get_files([$id]);

        return (!empty($files)) ? reset($files) : null;
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
        if (!Loader::includeModule('itgro')) {
            return [];
        }

        return Module::check($modules, $throwable);
    }
}

if (!function_exists('get_file_link_with_time')) {
    /**
     * Добавлят к ссылке метку времени последнего изменения этого файла (чтобы корректно работло кеширование в определённых случаях)
     *
     * @param string $link
     * @return string
     */
    function get_file_link_with_time($link)
    {
        return sprintf('%s?t=%s', $link, filemtime($_SERVER['DOCUMENT_ROOT'] . $link));
    }
}
