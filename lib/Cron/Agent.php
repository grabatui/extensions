<?php

namespace Itgro\Cron;

abstract class Agent
{
    protected $name = null;
    protected $parameters = [];

    public function getInitialSettings()
    {
        return [get_called_class(), $this->getName(), $this->getParameters()];
    }

    private function getName()
    {
        if ($this->name) {
            return $this->name;
        }

        // \Itgro\Cron\Agent -> itgro_cron_agent
        return camel_to_snake(get_called_class());
    }

    private function getParameters()
    {
        if (empty($this->parameters)) {
            return [];
        }

        return array_map(function ($parameter) {
            return sprintf('$%s', $parameter);
        }, $this->parameters);
    }
}
