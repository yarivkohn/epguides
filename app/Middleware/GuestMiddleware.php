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
use Slim\Flash\Messages as Flash;


class GuestMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        if($this->_container->get(Auth::class)->isLoggedin()){
//            $this->_container->get(Flash::class)->addMessage('info', 'You are already sign to this service');
            return $response->withRedirect($this->_container->get('router')->pathFor('home'));
        }

        $response = $next($request,$response);
        return $response;
    }
}