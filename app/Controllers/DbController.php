<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/8/18
 * Time: 8:31 AM
 */

namespace Epguides\Controllers;

use Epguides\Models\EpisodesDb;
use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DbController
{
    public function update(Request $request, Response $response, Twig $view)
    {
        $model = new EpisodesDb();
        $model->updateDbData();
    }
}