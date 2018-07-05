<?php

namespace Itgro;

class Session
{
    const MAIN_DATA_CONTAINER = 'itgro_data';

    public static function set($code, $value)
    {
        self::init();

        $_SESSION[self::MAIN_DATA_CONTAINER][$code] = $value;
    }

    public static function expand($code, $value)
    {
        self::init();

        $_SESSION[self::MAIN_DATA_CONTAINER][$code] = expand_variable(
            array_get($_SESSION[self::MAIN_DATA_CONTAINER], $code),
            $value
        );
    }

    public static function get($code)
    {
        return array_get($_SESSION, sprintf('%s.%s', self::MAIN_DATA_CONTAINER, $code));
    }

    public static function remove($code)
    {
        if (array_key_exists($code, $_SESSION[self::MAIN_DATA_CONTAINER])) {
            unset($_SESSION[self::MAIN_DATA_CONTAINER][$code]);
        }
    }

    private static function init()
    {
        if (!array_key_exists(self::MAIN_DATA_CONTAINER, $_SESSION)) {
            $_SESSION[self::MAIN_DATA_CONTAINER] = [];
        }
    }
}
