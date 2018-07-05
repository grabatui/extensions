<?php

use Bitrix\Main\Event;
use Bitrix\Main\EventResult;

if (!function_exists('execute_event')) {
    /**
     * Выполняет обработчик события и применяет к каждому результату обёртку, если нужно
     *
     * @param string $moduleId
     * @param string $eventName
     * @param callable|null $resultCallback
     */
    function execute_event($moduleId, $eventName, $resultCallback = null)
    {
        $event = new Event($moduleId, $eventName);
        $event->send();

        if (!$resultCallback || !is_callable($resultCallback) || !$event->getResults()) {
            return;
        }

        foreach ($event->getResults() as $eventResult) {
            call_user_func($resultCallback, $eventResult);
        }
    }
}

if (!function_exists('collect_event_handlers')) {
    /**
     * Собирает все обработчики для события
     *
     * @param string $moduleId
     * @param string $eventName
     * @return array
     */
    function collect_event_handlers($moduleId, $eventName)
    {
        $handlers = [];
        execute_event($moduleId, $eventName, function (EventResult $eventResult) use (&$handlers) {
            if ($eventResult->getType() !== EventResult::SUCCESS) {
                return;
            }

            $newHandlers = $eventResult->getParameters();

            if (!is_array($newHandlers) || empty($newHandlers)) {
                return;
            }

            $handlers = array_merge($handlers, $newHandlers);
        });

        return $handlers;
    }
}
