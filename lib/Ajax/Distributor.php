<?php

namespace Itgro\Ajax;

use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Itgro\WithExpandableHandlers;

class Distributor
{
    use WithExpandableHandlers;

    const EXPAND_HANDLERS_EVENT = 'onCreateAjaxHandlersList';

    protected static $handlers = [];

    public static function handleFromRequest()
    {
        $parts = explode('/', request()->get('handler'));
        $parts = array_filter($parts);

        $handler = array_shift($parts);
        $method = array_shift($parts);

        return self::handle($handler, $method, $parts);
    }

    /**
     * @param string $handler
     * @param string $method
     * @param mixed $arguments,...
     * @return mixed|null
     */
    public static function handle($handler, $method, $arguments = [])
    {
        $handlers = (new static)->getHandlers();

        if (!array_key_exists($handler, $handlers)) {
            return null;
        }

        $handler = $handlers[$handler];
        $handler = new $handler;

        if (!method_exists($handler, $method)) {
            return null;
        }

        if (func_num_args() > 3) {
            $arguments = array_slice(func_get_args(), 2);
        }

        return call_user_func_array([$handler, $method], $arguments);
    }
}
