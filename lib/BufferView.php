<?php

namespace Itgro;

class BufferView
{
    protected $views = [];
    protected $groups = [];

    protected $currentView;

    protected static $instance;

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new BufferView;
        }

        return self::$instance;
    }

    /**
     * @param string $viewName
     * @param string|null $groupName
     */
    public function start(string $viewName, $groupName = null)
    {
        $this->currentView = [
            'name' => $viewName,
            'group' => $groupName,
        ];

        ob_start();
    }

    /**
     * @param string|null $viewName
     * @return string
     */
    public function end($viewName = null)
    {
        $content = ob_get_clean();

        $viewName = (is_null($viewName)) ? $this->currentView['name'] : $viewName;

        $this->add($viewName, $content);

        return $content;
    }

    /**
     * @param string $viewName
     * @param mixed $content
     */
    public function add(string $viewName, $content)
    {
        application()->AddViewContent($viewName, $content);

        if (strlen($this->currentView['group']) > 0) {
            $this->groups[$this->currentView['group']][] = $this->currentView['name'];
        } else {
            $this->views[] = $viewName;
        }

        $this->currentView = [];
    }

    /**
     * @param string $viewName
     * @return bool
     */
    public function isViewExists(string $viewName)
    {
        return (strlen($viewName) > 0 && in_array($viewName, $this->views));
    }

    /**
     * @param string $groupName
     * @return bool
     */
    public function isGroupExists(string $groupName)
    {
        return (strlen($groupName) > 0 && array_key_exists($groupName, $this->groups));
    }

    /**
     * @param string $viewName
     */
    public function show(string $viewName)
    {
        application()->ShowViewContent($viewName);
    }

    /**
     * @param string $groupName
     */
    public function showGroup(string $groupName)
    {
        if (!array_key_exists($groupName, $this->groups)) {
            return;
        }

        foreach ($this->groups[$groupName] as $viewName) {
            application()->ShowViewContent($viewName);
        }
    }
}
