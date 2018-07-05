<?php

namespace Itgro;

class Router
{
    const DEFAULT_ROUTES_FILE_PATH = '/routes.php';

    private static $routes = null;

    public static function getByCode(string $code, $replaces = [])
    {
        self::setRoutes();

        $url = array_get(self::$routes, $code, $code);

        if (!empty($replaces) && $url) {
            $url = str_replace(array_keys($replaces), array_values($replaces), $url);
        }

        return $url;
    }

    private static function setRoutes()
    {
        if (is_array(self::$routes)) {
            return;
        }

        self::$routes = include_once($_SERVER['DOCUMENT_ROOT'] . self::getRoutesPath());

        self::$routes = (!self::$routes) ? [] : self::$routes;
    }

    private static function getRoutesPath()
    {
        if (defined('EXTENSIONS_ROUTES_PATH') && strlen(EXTENSIONS_ROUTES_PATH) > 0) {
            return EXTENSIONS_ROUTES_PATH;
        }

        return self::DEFAULT_ROUTES_FILE_PATH;
    }
}
