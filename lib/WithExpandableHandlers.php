<?php

namespace Itgro;

trait WithExpandableHandlers
{
    public function setHandlers($handlers = [])
    {
        static::$handlers = array_merge(
            static::$handlers,
            (is_array($handlers) && !empty($handlers)) ? $handlers : []
        );
    }

    public function getHandlers()
    {
        if (!defined('self::EXPAND_HANDLERS_EVENT')) {
            return static::$handlers;
        }

        static::$handlers = (!is_array(static::$handlers)) ? [] : static::$handlers;

        return array_merge(
            static::$handlers,
            collect_event_handlers('extensions', static::EXPAND_HANDLERS_EVENT)
        );
    }
}
