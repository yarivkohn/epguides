<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/6/18
 * Time: 4:53 PM
 */


use Epguides\App;
use Illuminate\Database\Capsule\Manager as Capsule;

session_start();

require_once  __DIR__.DS.'..'.DS.'vendor'.DS.'autoload.php';

$app = new App;

$capsule = new Capsule();
$capsule->addConnection([
	'driver' => 'mysql',
	'host' => 'localhost',
	'database' => 'epguides',
	'username' => 'root',
	'password' => 'root',
	'charset' => 'utf8',
	'collation' => 'utf8_unicode_ci',
	//    'prefix' => 'some_prefix'
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

$container = $app->getContainer();
$app->add(new \Epguides\Middleware\ValidationErrorMiddleware($container));


require_once  __DIR__.DS.'..'.DS.'app'.DS.'routes.php';
