<?php

namespace Itgro;

use CEventLog;
use Throwable;

class Log
{
    public static function add($code, $value, $severity = 'DEBUG', $module = 'extensions', $id = 0)
    {
        CEventLog::Log($severity, $code, $module, $id, (!is_string($value)) ? print_r($value, true) : $value);
    }

    public static function exception(Throwable $exception, $code, $module = 'extensions', $id = 0)
    {
        self::add($code, $exception->getMessage(), 'ERROR', $module, $id);
    }
}
