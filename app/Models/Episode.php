<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/13/18
 * Time: 2:48 PM
 */

namespace Epguides\Models;

use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
	const MAX_LIFE_TIME = 60 * 60 * 6; // 6 Hrs

	protected $fillable = [
		'name'                     ,
		'show_id'                  ,
		'last_episode_season'      ,
		'last_episode_number'      ,
		'last_episode_release_date',
		'next_episode_season'      ,
		'next_episode_number'      ,
		'next_episode_release_date',
		'sms_sent',
	];

	public function getFollowedShows() {
		$list = [];
		if(file_exists($this->cacheDir()) &&
			is_readable($this->cacheDir()) &&
			filemtime($this->cacheDir()) > time() - self::MAX_LIFE_TIME){
			$list = json_decode(file_get_contents($this->cacheDir()));
		} else {
			foreach($this->getListOfShows() as $show){
				$episodeData = $show->getAttributes();
				$list[]  = $this->createEpisodeObject($episodeData);
			}
//			file_put_contents($this->cacheDir(), json_encode($list));
		}
		return $list;
	}

	/**
	 * @return string
	 */
	private function cacheDir()
	{
		return __DIR__ . DS . '..' . DS . '..' . DS . 'resources' . DS . 'cache' . DS . 'cache.json';
	}

	private function getListOfShows() {
		$t = $this->whereNotNull('next_episode_release_date');
		return $t->get();
	}

	/**
	 * @param $episodeData
	 * @return \stdClass
	 */
	private function createEpisodeObject($episodeData) {
		$episode                         = new \stdClass();
		$episode->lastEpisode = new \stdClass();
		$episode->nextEpisode = new \stdClass();
		$episode->name                      = $episodeData['name'];
		$episode->lastEpisode->season       = $episodeData['last_episode_season'];
		$episode->lastEpisode->number       = $episodeData['last_episode_number'];
		$episode->lastEpisode->release_date = $episodeData['last_episode_release_date'];
		$episode->nextEpisode->season       = $episodeData['next_episode_season'];
		$episode->nextEpisode->number       = $episodeData['next_episode_number'];
		$episode->nextEpisode->release_date = $episodeData['next_episode_release_date'];
		$this->addDisplayDecorations($episode);
		return $episode;
	}


	/**
	 * If episode was released in the past week, add "is_new" tag.
	 * If episode was released more than a week ago but less than 2 weeks, add "getting_older" tag.
	 * @param $episode
	 */
	private function isNewRelease($episode) {
    	$today = new \DateTime();
    	$releaseDate = date_create_from_format('Y-m-d',$episode->release_date);
		$diff = $today->diff($releaseDate);
		if($diff->days > 0 && $diff->days < 7){
			$episode->is_new = true;
		}

		if($diff->days >= 7 && $diff->days < 14){
			$episode->is_getting_older = true;
		}


	}

	/**
	 * If release date is getting close (less than 3 days),
	 * add "almost_released" tag.
	 * @param $episode
	 */
	private function isReleaseDateClose($episode) {
		$today = new \DateTime();
		$releaseDate = date_create_from_format('Y-m-d',$episode->release_date);
		$diff = $today->diff($releaseDate);
		if($diff->days < 3 ){
			$episode->almost_released = true;
		}
	}

	/**
	 * @param $episode
	 */
	private function addDisplayDecorations($episode) {
		$this->isNewRelease($episode->lastEpisode);
		$this->isReleaseDateClose($episode->nextEpisode);
	}

}