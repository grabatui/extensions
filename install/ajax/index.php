<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

if (!is_ajax()) {
    abort_404();
}

if (!\Bitrix\Main\Loader::includeModule('extensions')) {
    die(json_encode('Ошибка во время подключения модуля Айтигро'));
}

try {
    $result = \Itgro\Ajax\Distributor::handleFromRequest();
} catch (Throwable $exception) {
    $result = $exception->getMessage();
}

if (is_object($result) && method_exists($result, 'toArray')) {
    $result = $result->toArray();
}

die(json_encode($result));
