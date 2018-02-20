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

        public function postChangePassword(Request $request, Response $response)
        {

        }
}

