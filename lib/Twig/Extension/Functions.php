<?php

namespace Itgro\Twig\Extension;

use Bitrix\Main\Config\Option;
use Itgro\Twig\CanHandle;
use Itgro\WithExpandableHandlers;
use Twig_Environment;
use Twig_SimpleFunction;

class Functions implements CanHandle
{
    use WithExpandableHandlers;

    const EXPAND_HANDLERS_EVENT = 'onCreateTwigFunctionsList';

    protected $handlers = [
        'include_file' => [self::class, 'includeFile'],
        'include_editable_file' => [self::class, 'includeEditableFile'],
        'option' => [self::class, 'getOption'],
    ];

    public static function handle(Twig_Environment $engine)
    {
        foreach ((new self)->getHandlers() as $function => $functionHandler) {
            $engine->addFunction(new Twig_SimpleFunction($function, $functionHandler));
        }
    }

    public static function includeFile($path)
    {
        include_file($path);
    }

    public static function includeEditableFile($path, $params = [])
    {
        include_editable_file($path, 'html', $params);
    }

    public static function getOption($moduleId, $optionCode)
    {
        return Option::get($moduleId, $optionCode);
    }

}
