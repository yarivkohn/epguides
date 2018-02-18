<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/18/18
 * Time: 4:34 PM
 */

namespace Epguides\Controllers;


use Epguides\Models\User;
use Epguides\Validation\Validator;
use Respect\Validation\Validator as v;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Router;
use Slim\Views\Twig;

class AuthController
{

		public function getSignUp(Request $request, Response $response, Twig $view)
		{
			return $view->render($response, 'auth/signup.twig');
		}

		public function postSignUp(Request $request, Response $response, Router $router, Validator $validator)
		{

		    $validator->validator($request, [
		        'email' => v::email(),
		        'name' => v::notEmpty()->alpha(),
		        'password' =>  v::noWhitespace()->notEmpty(),
            ]);

		    if($validator->failed()){
                return $response->withRedirect($router->pathFor('auth.signup') );
            }

			$user= new User();
			$user->create(
				[
					'name' => $request->getParam('name'),
					'email' => $request->getParam('email'),
					'password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT),
				]
			);

			return $response->withRedirect($router->pathFor('home') );

		}

}

