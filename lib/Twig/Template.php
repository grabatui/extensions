<?php

namespace Itgro\Twig;

use Maximaster\Tools\Twig\TemplateEngine;

class Template
{
    const CUSTOM_TEMPLATES_DIR_TEMPLATE = '/local/twig/%s.twig';

    public static function print(string $path, array $context = [])
    {
        echo self::render(self::normalizePath($path), $context);
    }

    public static function get(string $path, array $context = [])
    {
        return self::render(self::normalizePath($path), $context);
    }

    /**
     * From mail.request to /local/twig/mail/request.twig
     *
     * @param string $path
     * @return string
     */
    private static function normalizePath(string $path): string
    {
        $path = str_replace('.', '/', $path);

        return sprintf(self::CUSTOM_TEMPLATES_DIR_TEMPLATE, $path);
    }

    private static function render($path, array $context = [])
    {
        return TemplateEngine::renderStandalone($path, $context);
    }
}
