<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/8/18
 * Time: 8:20 AM
 */

use Epguides\Auth\Auth;
use Epguides\Models\Episode;
use Epguides\Models\Show;
use Epguides\Validation\Validator;
use Interop\Container\ContainerInterface;
use Epguides\Models\ViewAll;
use Slim\Csrf\Guard;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

return [
    'router' => DI\object(Slim\Router::class),
    Twig::class => function (ContainerInterface $c) {
        $twig = new Twig(__DIR__ . DS . '..' . DS . 'resources' . DS . 'views', [
            'debug' => true,
            'cache' => false,
        ]);
        $twig->addExtension(new TwigExtension(
            $c->get('router'),
            $c->get('request')->getUri()
        ));

        $twig->getEnvironment()->addGlobal('auth', [
            'check' => $c->get(Auth::class)->isLoggedIn(),
            'user' => $c->get(Auth::class)->user(),
        ]);

        $twig->addExtension(new Twig_Extension_Debug());
        return $twig;
    },
    ViewAll::class => function(ContainerInterface $c) {
        return new ViewAll();
    },

    Show::class => function(ContainerInterface $c) {
        return new Show();
    },

    Episode::class =>function(ContainerInterface $c)
    {
    	return new Episode();
    },

    Validator::class => function(ContainerInterface $c)
    {
        return new Validator();
    },

    Guard::class => function(ContainerInterface $c)
    {
    	return new Guard();
    },

    Auth::class => function(ContainerInterface $c)
    {
        return new Auth();
    }
];