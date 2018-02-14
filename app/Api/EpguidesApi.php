<?php

/**
 * Created by PhpStorm.
 * User: fisha
 * Date: 27/02/2016
 * Time: 8:02 AM
 */


namespace Epguides\Api;

use Epguides\Api\Sms\SmsSender;
use http\Exception;

class EpguidesApi
{

    const EPGUIDES_API_URL = 'http://epguides.frecar.no/show/';

    private $httpCode;
    private $responseBody;

	/**
	 * EpguidesApi constructor.
	 */
	public function __construct() {

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
            $this->responseBody = curl_exec($ch);
            $this->httpCode     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ( !preg_match( '/^2\d{2}$/', $this->httpCode ) ) {
                return false;
            }
            } catch (Exception $curlFault) {

            }
        return $this->responseBody;
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
//        $this->isReleaseDateClose($result->episode);
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
//        $this->isNewRelease($result->episode);
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

//	/**
//	 * If episode was released in the past week, add "is_new" tag.
//	 * If episode was released more than a week ago but less than 2 weeks, add "getting_older" tag.
//	 * @param $episode
//	 */
//	private function isNewRelease($episode) {
//    	$today = new \DateTime();
//    	$releaseDate = date_create_from_format('Y-m-d',$episode->release_date);
//		$diff = $today->diff($releaseDate);
//		if($diff->days > 0 && $diff->days < 7){
//			$smsHandler = new SmsSender();
//			$smsHandler->sendNotification($episode);
//			$episode->is_new = true;
//		}
//
//		if($diff->days >= 7 && $diff->days < 14){
//			$episode->is_getting_older = true;
//		}
//
//
//	}

//	/**
//	 * If release date is getting close (less than 3 days),
//	 * add "almost_released" tag.
//	 * @param $episode
//	 */
//	private function isReleaseDateClose($episode) {
//		$today = new \DateTime();
//		$releaseDate = date_create_from_format('Y-m-d',$episode->release_date);
//		$diff = $today->diff($releaseDate);
//		if($diff->days < 3 ){
//			$episode->almost_released = true;
//		}
//	}

}