<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/18/18
 * Time: 4:09 PM
 */

namespace Epguides\Models;

use Illuminate\Database\Eloquent\Model;

class Eztv  extends Model
{

	const EZTV_API_URL = 'https://eztv.ag//api/get-torrents';

	public function getMagnetLink($episodeData)
	{
		list($episodeImdbData['season'], $episodeImdbData['episode'], $episodeImdbData['imdb_id']) = explode(',',base64_decode($episodeData));
		$torrentsData = $this->getAllTorrents($episodeImdbData['imdb_id']);
        $numberOfPages = $this->getPagesNumber($torrentsData);
		$magnet = $this->findEpisodeMagnetLink($numberOfPages, $episodeData);
	}

	private function getAllTorrents($imdbId, $page=1)
	{
		$imdbId = substr($imdbId, 2);
        $queryParams = http_build_query([
            'imdb_id' =>$imdbId,
            'page' => $page,
        ]);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::EZTV_API_URL.$queryParams);
		curl_setopt($ch, CURLOPT_HEADER, 0);            // No header in the result
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return, do not echo result

		$raw_data = curl_exec($ch);
		curl_close($ch);
		$data = json_decode($raw_data);
		return $data;
	}


	private function findEpisodeMagnetLink($numberOfPages, $episodeData) {
		$magnetFound = false;
		$magnetUrl = '';
		for($page=1; $page<=$numberOfPages; $page++ ){
            if($magnetFound){
                break;
            }
		    $torrentList = $this->getAllTorrents($episodeData['imdb_id'], $page );
            foreach($torrentList->torrents as $torrent) {
                if ($magnetFound) {
                    break; //TODO can I break 2 when using 2 different loops?
                }
                if ($torrent->season == $episodeData['season'] && $torrent->episode == $episodeData['episode']) {
                    $magnetFound = true;
                    $magnetUrl = $torrent->magnet_url;
                }
            }
        }
		return $magnetUrl;
	}

    private function getPagesNumber($torrentList)
    {
        $totalResults = $torrentList->total_results;
        $resultsPerPage = $torrentList->results_per_page;
        return (int)($totalResults / $resultsPerPage);
    }

}