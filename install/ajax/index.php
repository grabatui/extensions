<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

if (mb_strtolower(array_get($_SERVER, 'HTTP_X_REQUESTED_WITH', '')) !== 'xmlhttprequest') {
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

die(json_encode($result));
