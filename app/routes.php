<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/8/18
 * Time: 8:30 AM
 */

use Epguides\Middleware\GuestMiddleware;

//HTTP GET ACTIONS

$app->get('/eztv/{episodeData}',   ['Epguides\Controllers\TorrentController', 'getMagnetLink'])->setName('eztv.getMagnet');
$app->get('/update', ['Epguides\Controllers\DbController', 'update'])->setName('updateDb');


$app->group('', function() use($app){

    //HTTP AUTH GET ACTIONS
    $app->get('/', ['Epguides\Controllers\HomeController', 'show'])->setName('home');
    $app->get('/show/favorite', ['Epguides\Controllers\HomeController', 'showFavorite'])->setName('show.unfiltered');
	$app->get('/show/all',['Epguides\Controllers\HomeController','showAll'])->setName('show.all');

	$app->get('/show/add/{showName}/{apiId}/{imdbId}',['Epguides\Controllers\DbController','addNewShowToWatchList'])->setName('show.add.new');
	$app->get('/show/delete/{showName}',['Epguides\Controllers\DbController','removeShowFromWatchList'])->setName('show.remove');

	$app->get('/auth/signout', ['Epguides\Controllers\AuthController', 'getSignOut'])->setName('auth.signout');
	$app->get('/auth/password/change', ['Epguides\Controllers\PasswordController', 'getChangePassword'])
        ->setName('auth.password.change');

    $app->get('/old', ['Epguides\Controllers\OldController', 'index'])->setName('backwardCompatibility');

    //HTTP AUTHED POST ACTIONS
    $app->post('/auth/password/change', ['Epguides\Controllers\PasswordController', 'postChangePassword']);

})->add(new \Epguides\Middleware\AuthMiddleware($container));


$app->group('', function() use($app){
    //HTTP GUEST GET ACTIONS
    $app->get('/auth/signup', ['Epguides\Controllers\AuthController', 'getSignUp'])->setName('auth.signup');
    $app->get('/auth/signin', ['Epguides\Controllers\AuthController', 'getSignIn'])->setName('auth.signin');

    //HTTP GUEST POST ACTIONS
    $app->post('/auth/signup', ['Epguides\Controllers\AuthController', 'postSignUp']);
    $app->post('/auth/signin', ['Epguides\Controllers\AuthController', 'postSignIn']);

})->add(new GuestMiddleware($container));