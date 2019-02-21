<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/8/18
 * Time: 8:31 AM
 */

namespace Epguides\Controllers;

use Epguides\Models\EloquentDb;
use Epguides\Models\Episode;
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

    public function updateCron(Router $router, EpisodesDb $episode)
    {
        $episode->updateDbData();
    }

	/**
	 * Add new show to watchlist
	 * @param          $showName
	 * @param          $apiId
	 * @param Request  $request
	 * @param Response $response
	 * @param Show     $show
	 * @param Router   $router
	 * @param Flash    $flash
	 * @return mixed
	 */
	public function addNewShowToWatchList($showName, $apiId, $imdbId, Request $request, Response $response, Show $show, Router $router, Flash $flash)
    {
		$show->create([
			'name' => $showName,
			'api_id' => $apiId,
            'user_id' => $_SESSION['user'],
			'imdb_id' => $imdbId,
		]);

		$flash->addMessage('info', "{$showName} successfully added to your list. Please update in order to see new shows.");
	    return $response->withRedirect($router->pathFor('home') );
    }

	/**
	 * Remove show from watchlist
	 * @param          $showName
	 * @param Request  $request
	 * @param Response $response
	 * @param Router   $router
	 * @param Flash    $flash
     * @param Show $show
	 * @param EpisodesDb  $episodesDb
	 * @return mixed
	 */
	public function removeShowFromWatchList($showName, Request $request, Response $response, Router $router, Flash $flash, Show $show, EpisodesDb $episodesDb)
    {
	    try{
		    $episodesDb->removeShowAndEpisode($showName);
//  			$show = $show->where(['name'=> $showName])->where('user_id', $_SESSION['user'])->first();
//            $show->delete();
        } catch(\Exception $e){
		 	$flash->addMessage('error', "failed to removed from {$showName} your list");
		    //TODO: log error
		    return $response->withRedirect($router->pathFor('home') );
	    }
	    $flash->addMessage('info', "{$showName} successfully removed from your list");
	    return $response->withRedirect($router->pathFor('home') );
    }
}