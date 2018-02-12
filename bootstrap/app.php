<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/6/18
 * Time: 4:53 PM
 */


use Epguides\App;

session_start();

require_once  __DIR__.DS.'..'.DS.'vendor'.DS.'autoload.php';

$app = new App;

require_once  __DIR__.DS.'..'.DS.'app'.DS.'routes.php';
