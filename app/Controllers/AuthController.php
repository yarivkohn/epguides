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

class AuthController
{

    public function getSignOut(Request $request, Response $response, Auth $auth, Router $router)
    {
       $auth->logOut();
       return $response->withRedirect($router->pathFor('auth.signin'));

    }

    public function getSignUp(Request $request, Response $response, Twig $view)
    {
        return $view->render($response, 'auth/signup.twig');
    }

    public function getSignIn(Request $request, Response $response, Twig $view)
    {
        return $view->render($response, 'auth/signin.twig');
    }

    public function postSignUp(Request $request, Response $response, Router $router, Validator $validator, Auth $auth, Flash $flash)
    {

        $validator->validator($request, [
            'email' => v::notEmpty()->noWhitespace()->email()->emailAvailable(),
            'name' => v::notEmpty()->alpha(),
            'password' => v::noWhitespace()->notEmpty(),
        ]);

        if ($validator->failed()) {
            $flash->addMessage('error', 'Sign up failed. Please check you data and try again.');
            return $response->withRedirect($router->pathFor('auth.signup'));
        }

        $user = new User();
        $user->create(
            [
                'name' => $request->getParam('name'),
                'email' => $request->getParam('email'),
                'password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT),
            ]
        );

        $auth->attempt(
            $request->getParam('email'),
            $request->getParam('password')
        );

        $flash->addMessage('success', 'You have been signed up successfully');
        return $response->withRedirect($router->pathFor('home'));

    }

    public function postSignIn(Request $request, Response $response, Router $router, Validator $validator, Auth $auth, Flash $flash)
    {

        $validator->validator($request, [
            'email' => v::notEmpty()->noWhitespace()->email(),
            'password' => v::noWhitespace()->notEmpty(),
        ]);

        if ($validator->failed()) {
            return $response->withRedirect($router->pathFor('auth.signin'));
        }

        $success = $auth->attempt(
            $request->getParam('email'),
            $request->getParam('password')
        );

        if(false === $success){
            $flash->addMessage('error', 'Incorrect username or password');

            return $response->withRedirect($router->pathFor('auth.signin'));
        }

        return $response->withRedirect($router->pathFor('home'));

    }


}

