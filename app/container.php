<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/8/18
 * Time: 8:20 AM
 */

use Interop\Container\ContainerInterface;
use Epguides\Models\ViewAll;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

return [
    'router' => DI\object(Slim\Router::class),
    Twig::class => function(ContainerInterface $c){
    $twig = new Twig(__DIR__.DS.'..'.DS.'resources'.DS.'views', [
        'cache' => false,
        'debug' => true,
    ]);
    $twig ->addExtension(new TwigExtension(
       $c->get('router'),
       $c->get('request')->getUri()
    ));
    return $twig;
},
    ViewAll::class => function( ContainerInterface $c){
		return new ViewAll();
    }
];