<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/6/18
 * Time: 4:53 PM
 */


use Epguides\App;
use Illuminate\Database\Capsule\Manager as Capsule;
use Respect\Validation\Validator as v;
use Slim\Csrf\Guard;


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
$app->add(new \Epguides\Middleware\OldInputMiddleware($container));
$app->add(new \Epguides\Middleware\OldInputMiddleware($container));
try {
	$app->add($container->get(Guard::class));
} catch (\Psr\Container\NotFoundExceptionInterface $e) {
	//TODO: log error
} catch (\Psr\Container\ContainerExceptionInterface $e) {
	//TODO: log error
}

v::with('Epguides\Validation\Rules');

require_once  __DIR__.DS.'..'.DS.'app'.DS.'routes.php';
