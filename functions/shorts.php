<?php

use Bitrix\Main\Application;
use Bitrix\Main\EventManager;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\Loader;
use Bitrix\Main\Page\Asset;
use Itgro\BufferView;

if (!function_exists('asset')) {
    function asset(): Asset
    {
        return Asset::getInstance();
    }
}

if (!function_exists('request')) {
    function request(): HttpRequest
    {
        return Application::getInstance()->getContext()->getRequest();
    }
}

if (!function_exists('application')) {
    function application(): CMain
    {
        global $APPLICATION;

        return $APPLICATION;
    }
}

if (!function_exists('user')) {
    function user(): CUser
    {
        global $USER;

        return $USER;
    }
}

if (!function_exists('buffer_view')) {
    function buffer_view(): BufferView
    {
        return BufferView::getInstance();
    }
}

if (!function_exists('event_manager')) {
    function event_manager(): EventManager
    {
        return EventManager::getInstance();
    }
}
