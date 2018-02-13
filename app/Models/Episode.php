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
	protected $fillable = [
		'name'                     ,
		'show_id'                  ,
		'last_episode_season'      ,
		'last_episode_number'      ,
		'last_episode_release_date',
		'next_episode_season'      ,
		'next_episode_number'      ,
		'next_episode_release_date',
		'sms_sent',
	];

}