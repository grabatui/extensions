<?php

if (!function_exists('only_digits')) {
    /**
     * Исключительно цифры из любой строки
     * При отрицательном занчении $length - цифры будут обрезаться с конца строки
     *
     * @param string $string
     * @param int|null $length
     * @return string
     */
    function only_digits(string $string, $length = null): string
    {
        preg_match_all('/\d+/', $string, $matches);

        $digits = (!empty($matches)) ? implode('', $matches[0]) : '';

        if (strlen($digits) <= 0 || !is_numeric($length)) {
            return $digits;
        }

        return ($length > 0) ?
            substr($digits, 0, $length) : // Первые n
            substr($digits, $length); // Последние n
    }
}

if (!function_exists('convert_to_phone')) {
    /**
     * Форматирование передаваемого телефона в другой формат
     * По-умолчанию формат +7 (999) 999-99-99
     *
     * @param string $rawPhone
     * @param string $format
     * @param string $regExp
     * @return string
     */
    function convert_to_phone(
        string $rawPhone,
        string $format = '+7 (%s) %s-%s-%s',
        string $regExp = '(\d{3})(\d{3})(\d{2})(\d{2})'
    ): string
    {
        $phone = only_digits($rawPhone, 10);

        if (!$phone) {
            return $rawPhone;
        }

        preg_match(sprintf('/%s/', $regExp), $phone, $matches);

        if (is_array($matches)) {
            array_shift($matches);
        }

        $matches = array_values(array_filter($matches));

        if (empty($matches)) {
            return $rawPhone;
        }

        array_unshift($matches, $format);

        return call_user_func_array('sprintf', $matches);
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
    /**
     * Форматирует цену
     *
     * @param string $price
     * @param bool $format
     * @return string
     */
    function price_format($price, $format = false): string
    {
        return sprintf('%s ₽', ($format) ? number_format($price, 0, ',', ' ') : $price);
    }
}

if (!function_exists('percent_format')) {
    /**
     * Форматирует проценты
     *
     * @param int|float $percent
     * @param bool $format
     * @param bool $round
     * @return string
     */
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

if (!function_exists('get_parent_section_by_path')) {
    /**
     * Достаёт из урла "родительский" кусок
     *
     * @param string $path
     * @return string
     */
    function get_parent_section_by_path($path)
    {
        $path = explode('/', trim($path, '/'));

        return array_shift($path);
    }
}

if (!function_exists('get_plural_word')) {
    /**
     * Склонение слова в зависимости от количества
     *
     * @param int $count
     * @param string $v0
     * @param string $v1
     * @param string $v2
     * @return string
     */
    function get_plural_word(int $count, $v0 = 'предметов', $v1 = 'предмет', $v2 = 'предмета')
    {
        $l2 = substr($count, -2);
        $l1 = substr($count, -1);

        if ($l2 > 10 && $l2 < 20) {
            return $v0;
        }

        switch ($l1) {
            case 0:
                return $v0;
                break;

            case 1:
                return $v1;
                break;

            case 2:
            case 3:
            case 4:
                return $v2;
                break;

            default:
                return $v0;
                break;
        }
    }
}

if (!function_exists('upper')) {
    function upper($string)
    {
        $function = (function_exists('mb_strtoupper')) ? 'mb_strtoupper' : 'strtoupper';

        return call_user_func($function, $string);
    }
}
