<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 4/25/18
 * Time: 1:37 PM
 */

namespace Epguides\Controllers;

use Epguides\Models\Eztv;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TorrentController
{
	public function getMagnetLink($episodeData, Request $request, Response $response)
	{
		$model = new Eztv();
		$magentLink = $model->getMagnetLink($episodeData);
	}
}