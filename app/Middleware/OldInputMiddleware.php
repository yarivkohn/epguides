<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/18/18
 * Time: 10:34 PM
 */

namespace Epguides\Middleware;

use Slim\Views\Twig;

class OldInputMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        if(isset($_SESSION['old'])){
            $this->_container->get(Twig::class)->getEnvironment()->addGLobal('old', $_SESSION['old']);
        }
        $_SESSION['old'] = $request->getParams();
        $response = $next($request,$response);
        return $response;
    }
}