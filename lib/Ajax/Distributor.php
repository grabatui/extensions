<?php

namespace Itgro\Ajax;

use Bitrix\Main\Event;
use Bitrix\Main\EventResult;

class Distributor
{
    const CREATE_HANDLERS_LIST_EVENT = 'onCreateAjaxHandlersList';

    protected $handlers = [];

    public static function handleFromRequest()
    {
        $parts = explode('/', request()->get('handler'));
        $parts = array_filter($parts);

        $handler = array_shift($parts);
        $method = array_shift($parts);

        self::handle($handler, $method, $parts);
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

    public function getHandlers()
    {
        $handlers = $this->handlers;

        $event = new Event('extensions', self::CREATE_HANDLERS_LIST_EVENT);
        $event->send();

        if (!$event->getResults()) {
            return $handlers;
        }

        foreach ($event->getResults() as $eventResult) {
            if ($eventResult->getType() !== EventResult::SUCCESS) {
                continue;
            }

            $newHandlers = $eventResult->getParameters();

            if (!is_array($newHandlers) || empty($newHandlers)) {
                continue;
            }

            $handlers = array_merge($handlers, $newHandlers);
        }

        return $handlers;
    }
}
