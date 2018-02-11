<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/8/18
 * Time: 8:30 AM
 */

//HTTP GET ACTIONS
$app->get('/', ['Epguides\Controllers\HomeController', 'index'])->setName('home');
$app->get('/old', ['Epguides\Controllers\OldController', 'index'])->setName('backwardCompatibility');