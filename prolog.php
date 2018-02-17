<?php

require_once('functions/shorts.php');
require_once('functions/helpers.php');

$autoloadFile = $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

if (file_exists($autoloadFile)) {
    require_once($autoloadFile);
}
