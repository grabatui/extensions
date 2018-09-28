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

    private $handlers = [];

    public function register()
    {
        foreach ($this->getHandlers() as $handler) {
            if (!class_exists($handler)) {
                continue;
            }

            $handler = new $handler;

            if (!($handler instanceof Agent)) {
                continue;
            }

            list($class, $name, $parameters) = $handler->getInitialSettings();

            if (!method_exists($class, 'call')) {
                continue;
            }

            $agent = Template::get('system.agent', [
                'name' => $name,
                'class' => $class,
                'parameters' => (!empty($parameters)) ? $parameters : [],
            ]);

            try {
                eval($agent);
            } catch (Throwable $exception) {
                Log::exception($exception, 'dynamic_agent_error');
            }
        }
    }
}
