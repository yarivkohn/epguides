<?php

/**
 * Created by PhpStorm.
 * User: fisha
 * Date: 27/02/2016
 * Time: 8:02 AM
 */


namespace Epguides\Api;

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
}