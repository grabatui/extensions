<?php

use Carbon\Carbon;

if (!function_exists('get_carbon_from_time')) {
    function get_carbon_from_time($time, $defaultNow = true)
    {
        if (is_numeric($time)) {
            return Carbon::createFromTimestamp($time);
        } elseif (is_string($time)) {
            return Carbon::createFromTimestamp(strtotime($time));
        } elseif ($time instanceof DateTime || $time instanceof Date) {
            return Carbon::createFromTimestamp($time->getTimestamp());
        }

        return ($defaultNow) ? Carbon::now() : null;
    }
}

if (!function_exists('get_datetime_for_humans')) {
    function get_datetime_for_humans($time)
    {
        $time = get_carbon_from_time($time);

        if (!($time instanceof Carbon)) {
            return null;
        }

        if ($time->isToday()) {
            return 'Сегодня';
        }

        if ($time->isYesterday()) {
            return 'Вчера';
        }

        return $time->format('d.m.Y H:i:s');
    }
}

if (!function_exists('get_date_for_humans')) {
    function get_date_for_humans($date)
    {
        $date = get_carbon_from_time($date);

        if (!($date instanceof Carbon)) {
            return null;
        }

        return $date->format('d.m.Y');
    }
}
