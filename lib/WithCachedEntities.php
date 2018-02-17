<?php

namespace Itgro;

trait WithCachedEntities
{
    private static $cached = [];

    protected static function cacheSet(string $key, $value = null)
    {
        if (!array_key_exists(self::getClass(), self::$cached)) {
            self::$cached[self::getClass()] = [];
        }

        self::$cached[self::getClass()][$key] = $value;
    }

    protected static function cacheHas(string $key)
    {
        return array_key_exists($key, array_get(self::$cached, self::getClass(), []));
    }

    protected static function cacheGet(string $key)
    {
        return array_get(self::$cached, self::cacheCreateKey(self::getClass(), $key));
    }

    protected static function cacheCreateKey($keys)
    {
        if (empty(func_get_args())) {
            return null;
        }

        return implode('.', func_get_args());
    }

    private static function getClass()
    {
        return get_called_class();
    }
}
