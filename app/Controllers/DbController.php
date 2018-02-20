<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/8/18
 * Time: 8:31 AM
 */

namespace Epguides\Controllers;

use Epguides\Models\EpisodesDb;
use Epguides\Models\Show;
use Slim\Router;
use Slim\Flash\Messages as Flash;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class DbController
{
	/**
	 * Run Db data update
	 * @param Request    $request
	 * @param Response   $response
	 * @param Router     $router
	 * @param EpisodesDb $episode
	 * @return mixed
	 */
	public function update(Request $request, Response $response, Router $router, EpisodesDb $episode)
    {
        $episode->updateDbData();
	    return $response->withRedirect($router->pathFor('home') );
    }

    public function addNewShowToWatchList($showName, $apiId, Request $request, Response $response, Show $show, Router $router, Flash $flash)
    {
		$show->create([
			'name' => $showName,
			'api_id' => $apiId
		]);

		$flash->addMessage('info', "{$showName} successfully added to your list");
	    return $response->withRedirect($router->pathFor('home') );
    }
}