<?php

include_once('helpers/array.php');
include_once('helpers/debug.php');
include_once('helpers/event.php');
include_once('helpers/file.php');
include_once('helpers/getter.php');
include_once('helpers/string.php');
include_once('helpers/time.php');

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

if (!function_exists('is_ajax')) {
    function is_ajax()
    {
        return (request()->getHeader('X-Requested-With') == 'XMLHttpRequest');
    }
}
