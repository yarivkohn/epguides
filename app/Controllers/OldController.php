<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/8/18
 * Time: 8:31 AM
 */

namespace Epguides\Controllers;

use Epguides\Models\ViewAll;
use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class OldController
{
    public function index(Request $request, Response $response, Twig $view, ViewAll $viewShowsHandler){
        $listShows = $viewShowsHandler->drawTable();
    	return $view->render($response, 'backward.twig', ['shows' =>$listShows]);
    }
}