<?php

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

        $key = explode('.', $key);
        $innerKey = array_shift($key);

        if (array_key_exists($innerKey, $array)) {
            $array = $array[$innerKey];
        } else {
            return $default;
        }

        return array_get($array, implode('.', $key));
    }
}

if (!function_exists('array_wrap')) {
    /**
     * Оборачивает немассивное значение в массив
     *
     * @param mixed $value
     * @return array
     */
    function array_wrap($value)
    {
        return (is_array($value)) ? $value : [$value];
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

if (!function_exists('get_global_filter')) {
    /**
     * Создаёт глобальный фильтр для вызова компонента и возвращает его же название
     * Т.о. можно сразу его вызывать при заполнении параметров вызова компонента
     *
     * @param string $name
     * @param array $filter
     * @return mixed
     */
    function get_global_filter($name, $filter)
    {
        global ${$name};
        ${$name} = $filter;

        return $name;
    }
}
