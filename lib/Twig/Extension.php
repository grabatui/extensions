<?php

namespace Itgro\Twig;

use Bitrix\Main\EventResult;
use Exception;
use Twig_Environment;

class Extension
{
    public static $handlers = [];

    public static function expand(Twig_Environment $engine)
    {
        foreach (self::$handlers as $handler) {
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
