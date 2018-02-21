<?php

namespace Itgro\Twig;

use Bitrix\Main\EventResult;
use Exception;
use Itgro\Twig\Extension\Filters;
use Itgro\Twig\Extension\Functions;
use Itgro\WithExpandableHandlers;
use Twig_Environment;

class Extension
{
    use WithExpandableHandlers;

    const EXPAND_HANDLERS_EVENT = 'onCreateTwigExtensionsList';

    public static $handlers = [
        Functions::class,
        Filters::class,
    ];

    public static function expand(Twig_Environment $engine)
    {
        foreach ((new self)->getHandlers() as $handler) {
            try {
                $handler = new $handler();

                if (!($handler instanceof CanHandle)) {
                    continue;
                }

                $handler::handle($engine);
            } catch (Exception $exception) {
                continue;
            }
        }

        return new EventResult(EventResult::SUCCESS, [$engine]);
    }
}
