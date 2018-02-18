<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/18/18
 * Time: 10:34 PM
 */

namespace Epguides\Middleware;

use Slim\Views\Twig;

class ValidationErrorMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        if(isset($_SESSION['errors'])){
            $this->_container->get(Twig::class)->getEnvironment()->addGLobal('errors', $_SESSION['errors']);
            unset($_SESSION['errors']);
        }
        $response = $next($request,$response);
        return $response;
    }
}