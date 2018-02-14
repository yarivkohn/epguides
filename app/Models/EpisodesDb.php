<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/11/18
 * Time: 9:23 PM
 */

namespace Epguides\Models;

use Epguides\Api\EpguidesApi;
use Epguides\Api\Sms\SmsSender;
use Illuminate\Database\QueryException;

class EpisodesDb {

	private $_api;
    private $_model;
    private $_smsHandler;

    public function __construct()
    {
        if(!$this->_api){
            $this->_api = new EpguidesApi();
        }
        if(!$this->_model){
        	$this->_model = new Show();
        }

        if(!$this->_smsHandler){
        	$this->_smsHandler = new SmsSender();
        }
    }

    public function getFollowedShows($onlyShowEpisodesWithNextReleaseDate = true)
    {
            foreach($this->getListOfShows() as $name => $showCode){
                $show = new \stdClass();
                $show->name = $name;
	            $showId = $this->_model->where('api_id', $showCode )->first(['id'])->getAttributeValue('id');
	            $show->lastEpisode = $this->_api->lastEpisode($showCode);
	            $show->nextEpisode = $this->_api->nextEpisode($showCode);
	            $this->writeEpisodeToDb($show, $showId);
	            $this->_smsHandler->sendNewReleaseSms($show->lastEpisode, $showId);
            }

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
	 * @param $show
	 * @param $showId
	 */
	private function writeEpisodeToDb($show, $showId) {
		$episodeModel = new Episode();
		$episodeData = $episodeModel->where('last_episode_season',$show->lastEpisode->season)
									->where('last_episode_number', $show->lastEpisode->number)
									->where('show_id', $showId);
        $attributes = [
            'name'                      => isset($show->lastEpisode->title) ? $show->lastEpisode->title : 'n/a',
            'show_id'                   => $showId,
            'last_episode_season'       => $show->lastEpisode->season,
            'last_episode_number'       => $show->lastEpisode->number,
            'last_episode_release_date' => $show->lastEpisode->release_date,
            'next_episode_season'       => isset($show->nextEpisode->season) ? $show->nextEpisode->season : NULL,
            'next_episode_number'       => isset($show->nextEpisode->number) ? $show->nextEpisode->number : NULL,
            'next_episode_release_date' => isset($show->nextEpisode->release_date) ? $show->nextEpisode->release_date : NULL,
//            'sms_sent'                  => 0,
        ];
		if(!empty($episodeData->first())){
		    $episodeData->update($attributes);
		    return; //Episode is already in Db
		}
		try {
			$this->_model->episode()->create($attributes);
		} catch (QueryException $e) { //QueryException
			$isThisException = TRUE;
			//TODO: Log this error!
		}
	}
}