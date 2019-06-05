<?php

namespace Itgro\Cron;

abstract class Agent implements MustCall
{
    protected $name = null;
    protected $parameters = [];

    public function getInitialSettings()
    {
        return [get_called_class(), $this->getName(), $this->getParameters(), $this->getFunctionParameters()];
    }

    static protected function setInfinity($memoryLimit = '1G')
    {
        set_time_limit(0);

        ini_set('memory_limit', $memoryLimit);
    }

    static protected function resultParameters(...$parameters)
    {
        if (empty($parameters)) {
            return [];
        }

        $entity = new static;

        $result = [];
        $index = 0;
        foreach ($entity->parameters as $name => $type) {
            $value = array_get($parameters, $index);

            switch ($entity->parameters[$name]) {
                case 'string':
                    $result[] = sprintf('"%s"', $value);
                    break;

                case 'boolean':
                case 'bool':
                    $result[] = ($value) ? 'true' : 'false';
                    break;

                case 'integer':
                case 'int':
                default:
                    $result[] = (is_null($value)) ? 'null' : $value;
                    break;
            }

            $index++;
        }

        return $result;
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
        }, array_keys($this->parameters));
    }

    private function getFunctionParameters()
    {
        // TODO: Проверка на обязетльность параметра
        return array_map(function ($parameter) {
            return sprintf('%s = null', $parameter);
        }, $this->getParameters());
    }
}
