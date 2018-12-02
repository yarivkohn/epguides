<?php

namespace Epguides\Models;

use Epguides\Api\EpguidesApi;
use Illuminate\Database\Eloquent\Model;

/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/13/18
 * Time: 8:03 AM
 */

class Show extends Model
{

	protected $fillable = [
		'name',
		'api_id',
        'user_id',
		'imdb_id',
	];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function episode(){
		return $this->belongsTo(Episode::class);
	}

	/**
	 * Return full list of available shows from API
	 * @return mixed
	 */
	public function getAll() {
		$epguidesApi = new EpguidesApi();
		return $epguidesApi->getAllShows();
	}

	public function addNewShowToWatchList($showId, $showName)
	{

	}

}