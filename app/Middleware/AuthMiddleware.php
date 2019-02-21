<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/19/18
 * Time: 6:11 PM
 */

namespace Epguides\Middleware;

use Epguides\Auth\Auth;
use Slim\Views\Twig;

class AuthMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        if(!$this->_container->get(Auth::class)->isLoggedin()){
            return $response->withRedirect($this->_container->get('router')->pathFor('auth.signin'));
        }

        $response = $next($request,$response);
        return $response;
    }
}