<?php

namespace Itgro\Twig;

use Twig_Environment;

interface CanHandle
{
    public static function handle(Twig_Environment $engine);
}
