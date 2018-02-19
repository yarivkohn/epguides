<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/8/18
 * Time: 8:30 AM
 */

//HTTP GET ACTIONS
$app->get('/', ['Epguides\Controllers\HomeController', 'show'])->setName('home');
$app->get('/all', ['Epguides\Controllers\HomeController', 'showAll'])->setName('unfiltered');
$app->get('/update', ['Epguides\Controllers\DbController', 'update'])->setName('updateDb');
$app->get('/auth/signup', ['Epguides\Controllers\AuthController', 'getSignUp'])->setName('auth.signup');
$app->get('/auth/signin', ['Epguides\Controllers\AuthController', 'getSignIn'])->setName('auth.signin');
$app->get('/auth/signout', ['Epguides\Controllers\AuthController', 'getSignOut'])->setName('auth.signout');

$app->get('/old', ['Epguides\Controllers\OldController', 'index'])->setName('backwardCompatibility');


//HTTP POST ACTIONS
$app->post('/auth/signup', ['Epguides\Controllers\AuthController', 'postSignUp']);
$app->post('/auth/signin', ['Epguides\Controllers\AuthController', 'postSignIn']);

