<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/18/18
 * Time: 4:34 PM
 */

namespace Epguides\Controllers;


use Epguides\Auth\Auth;
use Epguides\Models\User;
use Epguides\Validation\Validator;
use Respect\Validation\Validator as v;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Router;
use Slim\Views\Twig;
use Slim\Flash\Messages as Flash;

class PasswordController
{

        public function getChangePassword(Request $request, Response $response, Twig $view)
        {
            $view->render($response, 'auth/changePass.twig');
        }

        public function postChangePassword(Request $request, Response $response, Validator $validator, Flash $flash, Router $router, Auth $auth)
        {
            $validator->validator($request, [
                'cpassword' => v::noWhitespace()->notEmpty()->matchesPassword($auth->user()->password),
                'password' => v::noWhitespace()->notEmpty(),
            ]);

            if($validator->failed()){
                $flash->addMessage('error', 'Password was not changed. Please check you data and try again.');
                return $response->withRedirect($router->pathFor('auth.password.change'));
            }

            $auth->user()->setPassword($request->getParam('password'));
            $flash->addMessage('success', 'Password has changed.');
            return $response->withRedirect($router->pathFor('home'));
        }
}

