<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/8/18
 * Time: 8:31 AM
 */

namespace Epguides\Controllers;

use Epguides\Models\Episode;
use Epguides\Models\User;
use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HomeController {
	/**
	 * Return list of shows with next release date only
	 *
	 * @param Request  $request
	 * @param Response $response
	 * @param Twig     $view
	 * @return Response
	 */
	public function show(Request $request, Response $response, Twig $view) {
		$model          = new Episode();
		$listOfAllShows = $model->getFollowedShows();

		return $this->drawView($response, $view, $listOfAllShows);
	}

	/**
	 * Return list of all shows exist in Db
	 *
	 * @param Request  $request
	 * @param Response $response
	 * @param Twig     $view
	 * @return Response
	 */
	public function showAll(Request $request, Response $response, Twig $view) {
		$model          = new Episode();
		$listOfAllShows = $model->getFollowedShows(TRUE);

		return $this->drawView($response, $view, $listOfAllShows);
	}

	/**
	 * Draw the view with relevant shows
	 *
	 * @param Response $response
	 * @param Twig     $view
	 * @param          $listOfAllShows
	 * @return Response
	 */
	private function drawView(Response $response, Twig $view, $listOfAllShows) {
		return $view->render($response, 'followed.twig', [
			'followed' => $listOfAllShows,
		]);
	}
}