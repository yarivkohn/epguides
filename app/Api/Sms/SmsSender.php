<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/14/18
 * Time: 1:49 PM
 */

namespace Epguides\Api\Sms;

use Epguides\Models\Episode;

class SmsSender {

	const USER_EMAIL       = 'yariv_kohn@yahoo.com';
//	const HASH             = '6408204f070b032298e7b6dac6bf4939a8162173cdd97beee637f7f0af277dba'; //correct HASH key
	const HASH             = '6408204f070b032298e7b6dac6bf4939a8162173cdd97beee637f7f0af277dbaabcder'; //Incorrect HASH key
	const SUBSCRIBER = '972502164884';

	private $_episodeHandler;
	private $_smsHandler;

	/**
	 * SmsSender constructor.
	 */
	public function __construct(){
		if(!$this->_episodeHandler){
			$this->_episodeHandler = new Episode();
		}

		if(!$this->_smsHandler){
			$this->_smsHandler = new Textlocal(self::USER_EMAIL, self::HASH);
		}
	}


	/**
	 * If this is a new release, sens SMS notification
	 * @param $episode
	 * @param $showId
	 */
	public function sendNewReleaseSms($episode, $showId) {
		$today       = new \DateTime();
		$releaseDate = date_create_from_format('Y-m-d', $episode->release_date);
		$diff = $today->diff($releaseDate);
		if ($diff->days>0 && $diff->days<7) {
			$this->sendNotification($episode, $showId);
		}
	}


	/**
	 * Send SMS notification for new releases,
	 * If SMS was sent mark the episode with flag and do not send again.
	 * @param $episode
	 * @param $showId
	 */
	private function sendNotification($episode, $showId) {
		$episodeData = $this->_episodeHandler->where('last_episode_season',$episode->season)
			->where('last_episode_number', $episode->number)
			->where('show_id', $showId);
		if(!empty($episodeData->first(['sms_sent']))){
			$messageSent = boolval($episodeData->first(['sms_sent'])->getAttribute('sms_sent'));
		} else {
			$messageSent = true; //In case of an error don't overflow with SMS
		}
		try {
			if(!$messageSent){
			$text = $episode->show->title. ' S'.$episode->season.'E'.$episode->number.' '. $episode->release_date;
			file_put_contents('sms.log', date('Y-m-d H:i:s').' - '.$text.PHP_EOL, FILE_APPEND);
			//TODO: upon next release, need to check what is the result of successful response. Only after successful SMS update Db status.
//			$result = $this->_smsHandler->sendSms(array(self::SUBSCRIBER), $text, 'New Episode');
			$episodeData->update(['sms_sent'=> '1']);
			}
		} catch (\Exception $e) {
			echo $e->getMessage();
		}
	}

}