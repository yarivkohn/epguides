<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/11/18
 * Time: 2:34 PM
 */

namespace Epguides\Models;

use Epguides\Api\EpguidesApi;

class ViewAll
{
    const WEEK_IN_SECONDS = 604800; // 60*60*24*7 sec,min,hrs,days

    private $followUpTvShows = array(
        'The walking dead' => 'walkingdead',
        'The big bang theory' => 'bigbangtheory',
        'New girl' => 'newgirl',
        'Modern Family' => 'modernfamily',
        'Brooklyn nine nine' => 'brooklynninenine',
        'The black list' => 'blacklist',
        'Sons of Anarchy' => 'sonsofanarchy',
        'Shameless' => 'shameless_us',
        'Silicon VAlley' => 'siliconvalley',
        'Sherlock holmes' => 'sherlock',
        'American dad' => 'americandad',
        'Family Guy' => 'familyguy',
        'Games of thrones' => 'GameofThrones',
        'How to get away with murder' => 'howtogetawaywithmurder',
        'Whitney' => 'whitney',

    );

    private $api;

	/**
	 * ViewAll constructor.
	 */
	public function __construct()
    {
        if (!$this->api) {
            $this->api = new EpguidesApi();
        }
    }

	/**
	 * @return string
	 */
	public function drawTable()
    {
        $html = '';
        $html .= "<table class='table' border='1'>";
        $html .= "<thead><th>Show name</th><th>Season</th><th>Last episode</th><th>Last episode release date</th><th>Next episode</th><th>Next episode release date</th></thead>";
        $html .= "<tbody>";
        $html .= $this->drawList();
        $html .= "</tbody>";
        $html .= "</table>";
        return $html;

    }

	/**
	 * @return string
	 */
	public function drawList()
    {
        $tableData = '';
        foreach ($this->followUpTvShows as $displayName => $show) {
            $last = $this->api->lastEpisode($show);
            $textColor = $this->isOldDate($last->release_date);
            $lastTableData = "<td style='color:{$textColor}'>{$last->release_date}</td>";
            $next = $this->api->nextEpisode($show);
            $episodeTableData = "<td>{$displayName}</td><td>{$last->season}</td><td>{$last->number}</td>";
            if (is_object($next)) {
                $nextTableData = "<td>{$next->number}</td><td>{$next->release_date}</td>";

            } else {
                $nextTableData = "<td></td><td>No next episode for now</td>";
            }

            $tableData .= "<tr>{$episodeTableData}{$lastTableData}{$nextTableData}</tr>";
        }
        return $tableData;
    }

    /**
     * Given date, return text color
     * Less that a week ago - blue.
     * Between one to two week - red
     * Older than two weeks - black.
     * @param $date
     * @return string
     */
    private function isOldDate($date)
    {
        $date = new \DateTime($date);
        $timestamp = $date->getTimestamp();
        $now = new \DateTime('now');
        $delta = $now->getTimestamp() - $timestamp;
        if ($delta < self::WEEK_IN_SECONDS) {
            $color = "blue";
        } else if ($delta > self::WEEK_IN_SECONDS && $delta < 2 * self::WEEK_IN_SECONDS) {
            $color = "red";
        } else {
            $color = "black";
        }
        return $color;
    }

}