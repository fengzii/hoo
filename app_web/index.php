<?php

$app_name = "app";

define('APP_NAME', $app_name);
define('APP_CONTORLLER', $app_name);
define('APP_MODEL', 'app');
define('APP_DATA', $app_name);

require '../system/dev.php';
//require '../system/core.php';
define('APP_TEMPLATE', APP_PATH.'views'.DIRECTORY_SEPARATOR);
require APP_PATH.'lib'.DIRECTORY_SEPARATOR.'App.php';
require APP_PATH.'lib'.DIRECTORY_SEPARATOR.'D.php';
require APP_PATH.'function'.DIRECTORY_SEPARATOR.'fun.php';
Application::run();
?>