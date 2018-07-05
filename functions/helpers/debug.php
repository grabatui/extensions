<?php

if (!function_exists('pre')) {
    /**
     * Дебажная распечатка
     *
     * @param mixed $value
     */
    function pre($value)
    {
        echo '<pre>';
        print_r($value);
        echo '</pre>';
    }
}

if (!function_exists('pre_d')) {
    /**
     * Дебажная распечатка с var_dump'ом
     *
     * @param mixed $value
     */
    function pre_d($value)
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
