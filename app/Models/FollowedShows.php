<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/11/18
 * Time: 9:23 PM
 */

namespace Epguides\Models;

use Epguides\Api\EpguidesApi;

class FollowedShows {

    const MAX_LIFE_TIME = 60 * 60 * 24 ; //24 Hrs cache
    private $_api;

    public function __construct()
    {
        if(!$this->_api){
            $this->_api = new EpguidesApi();
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
                $show->nextEpisode = $this->_api->nextEpisode($showCode);
                $show->lastEpisode = $this->_api->lastEpisode($showCode);
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
            file_put_contents($this->cacheDir(), json_encode($list));
        }
        return $list;
    }

    private function getListOfShows()
    {
    	$shows = array(
		    'The walking dead' => 'walkingdead',
		    'The big bang theory' => 'bigbangtheory',
		    'New girl' => 'newgirl',
		    'Modern Family' => 'modernfamily',
		    'Brooklyn nine nine' => 'brooklynninenine',
		    'The black list' => 'blacklist',
		    'Sons of Anarchy' => 'sonsofanarchy',
		    'Shameless' => 'shameless_us',
		    'Silicon Valley' => 'siliconvalley',
		    'Sherlock holmes' => 'sherlock',
		    'American dad' => 'americandad',
		    'Family Guy' => 'familyguy',
		    'Games of thrones' => 'GameofThrones',
		    'How to get away with murder' => 'howtogetawaywithmurder',
		    'Whitney' => 'whitney',

	    );
	    natcasesort($shows);
        return $shows;
    }

    /**
     * @return string
     */
    private function cacheDir()
    {
        return __DIR__ . DS . '..' . DS . '..' . DS . 'resources' . DS . 'cache' . DS . 'cache.json';
    }
}