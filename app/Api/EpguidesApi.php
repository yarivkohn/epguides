<?php

/**
 * Created by PhpStorm.
 * User: fisha
 * Date: 27/02/2016
 * Time: 8:02 AM
 */


namespace Epguides\Api;

use Epguides\Api\Sms\Textlocal;
use Epguides\Models\Episode;
use http\Exception;

class EpguidesApi
{

    const EPGUIDES_API_URL = 'http://epguides.frecar.no/show/';
	const USER_EMAIL       = 'yariv_kohn@yahoo.com';
	const HASH             = '6408204f070b032298e7b6dac6bf4939a8162173cdd97beee637f7f0af277dba';
    const SUBSCRIBER = '972502164884';

    private $http_code;
    private $response_body;
	private $_smsHandler;
	private $_episodeHandler;
	private $_showId;

    public function __construct() {
    	if(!$this->_smsHandler){
    		$this->_smsHandler = new Textlocal(self::USER_EMAIL, self::HASH);
	    }
	    if(!$this->_episodeHandler){
    		$this->_episodeHandler = new Episode();
	    }
    }

	/**
	 * @param mixed $showId
	 */
	public function setShowId($showId) {
		$this->_showId = $showId;
	}

	/**
     * Send API request to epguids.com server
     * @param $url
     * @return bool|mixed
     */
    private function sendApiRequest($url)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $this->response_body = curl_exec($ch);
            $this->http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ( !preg_match( '/^2\d{2}$/', $this->http_code ) ) {
                return false;
            }
            } catch (Exception $curlFault) {

            }
        return $this->response_body;
    }

    /**
     * Given specific episode, return if it was already released
     * @param $show
     * @param $season
     * @param $chapter
     * @return mixed
     */
    public function isSpecificEpisodeReleased($show, $season, $chapter)
    {
        $url = self::EPGUIDES_API_URL.$show."/".$season."/".$chapter."/released/";
        $result = json_decode($this->sendApiRequest($url));
        return $result->status;
    }

    /**
     * Given show name, return next episode release date
     * @param $show
     * @return mixed
     */
    public function nextEpisode($show)
    {
        $url = self::EPGUIDES_API_URL.$show."/next/";
        $result = json_decode($this->sendApiRequest($url));
        if(!$result){
            return "Unable to get next episode";
        }
        $this->isReleaseDateClose($result->episode);
        return $result->episode;
    }

    /**
     * Given show name, return last episode release date
     * @param $show
     * @return mixed
     */
    public function lastEpisode($show)
    {
        $url = self::EPGUIDES_API_URL.$show."/last/";
        $result = json_decode($this->sendApiRequest($url));
        $this->isNewRelease($result->episode);
        return $result->episode;
    }

    /**
     * Give show name, return last episode release date
     * @return mixed
     */
    public function getAllShows()
    {
        $url = self::EPGUIDES_API_URL;
        $result = json_decode($this->sendApiRequest($url));
        return $result;
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
			$this->sendNotification($episode);
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

	private function sendNotification($episode) {
		$episodeData = $this->_episodeHandler->where('last_episode_season',$episode->season)
			->where('last_episode_number', $episode->number)
			->where('show_id', $this->_showId)
			->first(['sms_sent']);
		if(!empty($episodeData)){
			$messageSent = boolval($episodeData->getAttribute('sms_sent')); //TODO: Set in Db that message was already sent to user.
		} else {
			$messageSent = true; //In case of an error don't overflow with SMS
		}
		$text = $episode->show->title. ' S'.$episode->season.'E'.$episode->number.' '. $episode->release_date;
		try {
			if(!$messageSent){
//			$result = $this->_smsHandler->sendSms(array(self::SUBSCRIBER), $text, 'New Episode');
			$this->_episodeHandler->where('last_episode_season',$episode->season)
				->where('last_episode_number', $episode->number)
				->where('show_id', $this->_showId)
				->update(['sms_sent'=> 1]);
			}
		} catch (\Exception $e) {
			echo $e->getMessage();
		}
	}
}