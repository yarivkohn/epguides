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

	const EZTV_API_URL = 'https://eztv.ag//api/get-torrents?imdb_id=';

	public function getMagnetLink($episodeData)
	{
		list($episodeImdbData['season'], $episodeImdbData['episode'], $episodeImdbData['imdb_id']) = explode(',',base64_decode($episodeData));
		$allTorrents = $this->getAllTorrents($episodeImdbData['imdb_id']);
		$magnet = $this->findEpisodeMagnetLink($allTorrents, $episodeImdbData['season'], $episodeImdbData['episode']);
	}

	private function getAllTorrents($imdbId, $page=1)
	{
		$imdbId = substr($imdbId, 2);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::EZTV_API_URL.$imdbId);
		curl_setopt($ch, CURLOPT_HEADER, 0);            // No header in the result
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return, do not echo result

		$raw_data = curl_exec($ch);
		curl_close($ch);
		$data = json_decode($raw_data);
		return $data;
	}


	private function findEpisodeMagnetLink($torrentList, $season, $episode) {
		$magnetFound = false;
		$magnetUrl = '';
		foreach($torrentList->torrents as $torrent){
			if($magnetFound){
				continue;
			}
			if($torrent->season == $season && $torrent->episode == $episode){
				$magnetFound = true;
				$magnetUrl = $torrent->magnet_url;
			}
		}
		return $magnetUrl;
	}

}