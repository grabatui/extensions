<?php

namespace Itgro\Twig\Extension;

use Itgro\Twig\CanHandle;
use Itgro\WithExpandableHandlers;
use Twig_Environment;
use Twig_SimpleFilter;

class Filters implements CanHandle
{
    use WithExpandableHandlers;

    const EXPAND_HANDLERS_EVENT = 'onCreateTwigFiltersList';

    protected static $handlers = [];

    public static function handle(Twig_Environment $engine)
    {
        foreach ((new self)->getHandlers() as $filter => $filterHandler) {
            $engine->addFunction(new Twig_SimpleFilter($filter, $filterHandler));
        }
    }
}
