<?php

namespace Itgro\Cron;

use Itgro\Log;
use Itgro\Twig\Template;
use Itgro\WithExpandableHandlers;
use Throwable;

class Kernel
{
    use WithExpandableHandlers;

    const EXPAND_HANDLERS_EVENT = 'onCollectDynamicAgents';

    protected static $handlers = [];

    public function register($handlers = [])
    {
        static::$handlers = ($handlers) ? $handlers : [];

        foreach ($this->getHandlers() as $handler) {
            if (!class_exists($handler)) {
                continue;
            }

            $handler = new $handler;

            if (!($handler instanceof Agent)) {
                continue;
            }

            list($class, $name, $parameters, $functionParameters) = $handler->getInitialSettings();

            if (!method_exists($class, 'call')) {
                continue;
            }

            $agent = Template::get('system.agent', [
                'name' => $name,
                'class' => $class,
                'parameters' => (!empty($parameters)) ? $parameters : [],
                'function_parameters' => (!empty($functionParameters)) ? $functionParameters : [],
            ]);

            try {
                eval($agent);
            } catch (Throwable $exception) {
                Log::exception($exception, 'dynamic_agent_error');
            }
        }
    }
}
