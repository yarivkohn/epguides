<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/6/18
 * Time: 4:51 PM
 */

define('DS', DIRECTORY_SEPARATOR);
define('ENV', getenv('ENV'));
define('DEVELOPER_MODE', getenv('DEVELOPER_MODE'));

//if('DEV' == ENV){
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
//}

require_once __DIR__.'/../bootstrap/app.php';

$app->run();