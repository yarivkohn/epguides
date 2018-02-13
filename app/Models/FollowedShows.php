<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/11/18
 * Time: 9:23 PM
 */

namespace Epguides\Models;

use Epguides\Api\EpguidesApi;
use Illuminate\Database\QueryException;

class FollowedShows {

    const MAX_LIFE_TIME = 60 * 60 * 24 ; //24 Hrs cache

	private $_api;
    private $_model;

    public function __construct()
    {
        if(!$this->_api){
            $this->_api = new EpguidesApi();
        }
        if(!$this->_model){
        	$this->_model = new Show();
        }
    }

    public function getFollowedShows($onlyShowEpisodesWithNextReleaseDate = true)
    {
        $list = [];
        if(file_exists($this->cacheDir()) &&
            is_readable($this->cacheDir()) &&
            filemtime($this->cacheDir()) > time() - self::MAX_LIFE_TIME){
            $list = json_decode(file_get_contents($this->cacheDir()));
        } else {
            foreach($this->getListOfShows() as $name => $showCode){
                $show = new \stdClass();
                $show->name = $name;
	            $showId = $this->_model->where('api_id', $showCode )->first(['id'])->getAttributeValue('id');
	            $this->_api->setShowId($showId);
                $show->nextEpisode = $this->_api->nextEpisode($showCode);
                $show->lastEpisode = $this->_api->lastEpisode($showCode);
	            $this->writeEpisodeToDb($show, $showId);
	            if(!isset($show->nextEpisode->release_date)){ //filter shows which currently doesn't have next show
	                if($onlyShowEpisodesWithNextReleaseDate){
	                	continue;
	                } else {
	                	$list[] = $show;
	                }
                } else {
                	$list[] = $show;
                }
            }
//            file_put_contents($this->cacheDir(), json_encode($list));
        }
        return $list;
    }

    private function getListOfShows()
    {
    	$formattedArray = array();
    	$shows = $this->_model->all()->toArray();
	    foreach($shows as $show){
	    	$formattedArray[$show['name']] = $show['api_id'];
	    }
	    natcasesort($formattedArray);
        return $formattedArray;
    }

    /**
     * @return string
     */
    private function cacheDir()
    {
        return __DIR__ . DS . '..' . DS . '..' . DS . 'resources' . DS . 'cache' . DS . 'cache.json';
    }

	/**
	 * @param $show
	 * @param $showId
	 */
	private function writeEpisodeToDb($show, $showId) {
		$episodeModel = new Episode();
		$episodeData = $episodeModel->where('last_episode_season',$show->lastEpisode->season)
									->where('last_episode_number', $show->lastEpisode->number)
									->where('show_id', $showId)
									->first();
		if(!empty($episodeData)){
			return; //Episode is already in Db
		}
		try {
			$this->_model->episode()->create([
				'name'                      => isset($show->nextEpisode->title) ? $show->nextEpisode->title : 'n/a',
				'show_id'                   => $showId,
				'last_episode_season'       => $show->lastEpisode->season,
				'last_episode_number'       => $show->lastEpisode->number,
				'last_episode_release_date' => $show->lastEpisode->release_date,
				'next_episode_season'       => isset($show->nextEpisode->season) ? $show->nextEpisode->season : NULL,
				'next_episode_number'       => isset($show->nextEpisode->number) ? $show->nextEpisode->number : NULL,
				'next_episode_release_date' => isset($show->nextEpisode->release_date) ? $show->nextEpisode->release_date : NULL,
				'sms_sent'                  => 0,
			]);
		} catch (QueryException $e) {
			$isThisException = TRUE;
			//TODO: Log this error!
		}
	}
}