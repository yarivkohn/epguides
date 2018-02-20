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
        'name',
        'show_id',
        'last_episode_season',
        'last_episode_number',
        'last_episode_release_date',
        'next_episode_season',
        'next_episode_number',
        'next_episode_release_date',
        'sms_sent',
    ];

    /**
     * @param bool $showall
     * @return array
     */
    public function getFollowedShows($showall = false)
    {
        $list = [];
        foreach ($this->getListOfShows($showall) as $show) {
            $episodeData = $show->getAttributes();
            $list[] = $this->createEpisodeObject($episodeData);
        }
        return $list;
    }

	public function getAllShows()
	{
		$list = [];
		$show = new Show();
		foreach ($show->getAll() as $show) {
			$list[$show->title] = $this->createAddEpisodeObject((array)$show);
//			$list[] = $this->createAddEpisodeObject((array)$show);
		}
		sort($list);
		return $list;
	}

    /**
     * Return list of episodes with their data
     * @param $showAll
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    private function getListOfShows($showAll = false)
    {
        $episodeList = $this->join('shows', 'show_id', '=', 'id', 'left')
            ->select('episodes.name',
                'episodes.last_episode_season',
                'episodes.last_episode_number',
                'episodes.last_episode_release_date',
                'episodes.next_episode_season',
                'episodes.next_episode_number',
                'episodes.next_episode_release_date',
                'shows.name as show_name');
        if (!$showAll) {
            $episodeList->whereNotNull('next_episode_release_date');
        }
        return $episodeList->get();
    }

    /**
     * @param $episodeData
     * @return \stdClass
     */
    private function createEpisodeObject($episodeData)
    {
        $episode = new \stdClass();
        $episode->lastEpisode = new \stdClass();
        $episode->nextEpisode = new \stdClass();
        $episode->name = $episodeData['name'];
        $episode->show_name = $episodeData['show_name'];
        $episode->lastEpisode->season = $episodeData['last_episode_season'];
        $episode->lastEpisode->number = $episodeData['last_episode_number'];
        $episode->lastEpisode->release_date = $episodeData['last_episode_release_date'];
        $episode->nextEpisode->season = $episodeData['next_episode_season'];
        $episode->nextEpisode->number = $episodeData['next_episode_number'];
        $episode->nextEpisode->release_date = $episodeData['next_episode_release_date'];
        $this->addDisplayDecorations($episode);

        return $episode;
    }


	/**
	 * @param $episodeData
	 * @return \stdClass
	 */
	private function createAddEpisodeObject($episodeData)
	{
		$episode = new \stdClass();
		$episode->name = $episodeData['title'];
		$episode->epguideName = $episodeData['epguide_name'];
		$episode->imdb = $episodeData['imdb_id'];
		$this->addDisplayDecorations($episode);
		return $episode;
	}


	/**
     * If episode was released in the past week, add "is_new" tag.
     * If episode was released more than a week ago but less than 2 weeks, add "getting_older" tag.
     *
     * @param $episode
     */
    private function isNewRelease($episode)
    {
    	if(isset($episode->release_date)){
		    $today = new \DateTime();
		    $releaseDate = date_create_from_format('Y-m-d', $episode->release_date);
		    if (isset($releaseDate) && $releaseDate !== false) {
			    $diff = $today->diff($releaseDate);
			    if ($diff->days > 0 && $diff->days < 7) {
				    $episode->is_new = TRUE;
			    }
			    if ($diff->days >= 7 && $diff->days < 14) {
				    $episode->is_getting_older = TRUE;
			    }
		    }
	    }
    }

    /**
     * If release date is getting close (less than 3 days),
     * add "almost_released" tag.
     *
     * @param $episode
     */
    private function isReleaseDateClose($episode)
    {
    	if(isset($episode->release_date)){
		    $today = new \DateTime();
		    $releaseDate = date_create_from_format('Y-m-d', $episode->release_date);
		    if (isset($releaseDate) && $releaseDate !== false) {
			    $diff = $today->diff($releaseDate);
			    if ($diff->days < 3) {
				    $episode->almost_released = TRUE;
			    }
		    }
	    }
    }

    /**
     * @param $episode
     */
    private function addDisplayDecorations($episode)
    {
    	if(isset($episode->lastEpisode)){
		    $this->isNewRelease($episode->lastEpisode);
	    }
        if(isset($episode->nextEpisode)){
	        $this->isReleaseDateClose($episode->nextEpisode);
        }
    }
}