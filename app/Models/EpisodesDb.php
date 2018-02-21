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
    private $_showModel;
    private $_smsHandler;
    private $_episodeModel;

	/**
	 * EpisodesDb constructor.
	 */
	public function __construct()
    {
        if(!$this->_api){
            $this->_api = new EpguidesApi();
        }
        if(!$this->_showModel){
        	$this->_showModel = new Show();
        }

	    if(!$this->_episodeModel){
		    $this->_episodeModel = new Episode();
	    }

        if(!$this->_smsHandler){
        	$this->_smsHandler = new SmsSender();
        }
    }


	/**
	 * Updates Db with new episode data.
	 * If new episode was released, sends SMS
	 */
	public function updateDbData()
    {
            foreach($this->getListOfShows() as $name => $showCode){
                $show = new \stdClass();
                $show->name = $name;
	            $showId = $this->_showModel->where('api_id', $showCode )->first(['id'])->getAttributeValue('id');
	            $show->lastEpisode = $this->_api->lastEpisode($showCode);
	            $show->nextEpisode = $this->_api->nextEpisode($showCode);
	            $this->writeEpisodeToDb($show, $showId);
	            $this->_smsHandler->sendNewReleaseSms($show->lastEpisode, $showId);
            }

    }

	/**
	 * @param $showName
	 * @throws \Exception
	 */
	public function removeShowAndEpisode($showName) {
		EloquentDb::beginTransaction();
		try {
			$show = $this->_showModel->where('name', $showName)->first();
			$this->_episodeModel->where('show_id', $show->getAttribute('id'))->delete();
			$show->delete();
			EloquentDb::commit();
		} catch (\Exception $e) {
			EloquentDb::rollback();
			throw new \Exception('failed to delete show data due to: '.$e->getMessage());
		}
	}


	/**
	 * Return list of all shows being followed
	 * @return array
	 */
	private function getListOfShows()
    {
    	$formattedArray = array();
    	$shows = $this->_showModel->all()->toArray();
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
		$episodeData = $episodeModel->where('show_id', $showId);

        $attributes = [
            'name'                      => isset($show->lastEpisode->title) ? $show->lastEpisode->title : 'n/a',
            'show_id'                   => $showId,
            'last_episode_season'       => $show->lastEpisode->season,
            'last_episode_number'       => $show->lastEpisode->number,
            'last_episode_release_date' => $show->lastEpisode->release_date,
            'next_episode_season'       => isset($show->nextEpisode->season) ? $show->nextEpisode->season : NULL,
            'next_episode_number'       => isset($show->nextEpisode->number) ? $show->nextEpisode->number : NULL,
            'next_episode_release_date' => isset($show->nextEpisode->release_date) ? $show->nextEpisode->release_date : NULL,
        ];
		if(!empty($episodeData->first())){
		    $episodeData->update($attributes);
		    return; //Episode is already in Db
		}
		try {
			$episodeData->delete();
			$this->_showModel->episode()->create(
				array_merge($attributes, array('sms_sent'=> 0))
			);
		} catch (QueryException $e) {
			$isThisException = TRUE;
			//TODO: Log this error!
		} catch (\Exception $e) {
			$isThisException = TRUE;
			//TODO: Log this error!
		}
	}
}