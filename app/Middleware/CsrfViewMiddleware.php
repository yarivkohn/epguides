<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/19/18
 * Time: 6:11 PM
 */

namespace Epguides\Middleware;

use Slim\Csrf\Guard;
use Slim\Views\Twig;

class CsrfViewMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        $this->_container->get(Twig::class)->getEnvironment()->addGLobal(
            'csrf', [
                'field' => '
                    <input type="hidden" name="'.$this->_container->get(Guard::class)->getTokenNameKey().'" 
                     value="'.$this->_container->get(Guard::class)->getTokenName().'">
                    <input type="hidden" name="'.$this->_container->get(Guard::class)->getTokenValueKey().'"
                     value="'.$this->_container->get(Guard::class)->getTokenValue().'">
                ',
        ]);

        $response = $next($request,$response);
        return $response;
    }
}