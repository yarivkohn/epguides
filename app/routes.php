<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/8/18
 * Time: 8:30 AM
 */

use Epguides\Middleware\GuestMiddleware;

//HTTP GET ACTIONS

$app->get('/update', ['Epguides\Controllers\DbController', 'update'])->setName('updateDb');


//HTTP POST ACTIONS


$app->group('', function() use($app){

    //HTTP AUTH GET ACTIONS
    $app->get('/', ['Epguides\Controllers\HomeController', 'show'])->setName('home');
    $app->get('/all', ['Epguides\Controllers\HomeController', 'showAll'])->setName('unfiltered');
    $app->get('/auth/signout', ['Epguides\Controllers\AuthController', 'getSignOut'])->setName('auth.signout');
    $app->get('/auth/password/change', ['Epguides\Controllers\PasswordController', 'getChangePassword'])
        ->setName('auth.password.change');

    $app->get('/old', ['Epguides\Controllers\OldController', 'index'])->setName('backwardCompatibility');

    //HTTP AUTHED POST ACTIONS
    $app->post('/auth/password/change', ['Epguides\Controllers\PasswordController', 'postChangePassword']);

})->add(new \Epguides\Middleware\AuthMiddleware($container));


$app->group('', function() use($app){
    //HTTP GUEST ONLY GET ACTIONS
    $app->get('/auth/signup', ['Epguides\Controllers\AuthController', 'getSignUp'])->setName('auth.signup');
    $app->get('/auth/signin', ['Epguides\Controllers\AuthController', 'getSignIn'])->setName('auth.signin');

    //HTTP GUEST ONLY POST ACTIONS
    $app->post('/auth/signup', ['Epguides\Controllers\AuthController', 'postSignUp']);
    $app->post('/auth/signin', ['Epguides\Controllers\AuthController', 'postSignIn']);

})->add(new GuestMiddleware($container));