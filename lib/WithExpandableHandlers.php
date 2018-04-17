<?php

namespace Itgro;

trait WithExpandableHandlers
{
    protected $expandHandlersEventName;

    public function getHandlers()
    {
        if (!defined('self::EXPAND_HANDLERS_EVENT')) {
            return $this->handlers;
        }

        return array_merge(
            $this->handlers,
            collect_event_handlers('extensions', self::EXPAND_HANDLERS_EVENT)
        );
    }
}
