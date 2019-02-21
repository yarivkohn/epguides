<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/18/18
 * Time: 4:09 PM
 */

namespace Epguides\Models;

use Illuminate\Database\Eloquent\Model;

class User  extends Model
{
	protected $fillable = [
		'name',
		'password',
		'email',
        'last_login',
        'user_ip',
	];

	public function setPassword($password)
    {
        $this->update(['password' => password_hash($password, PASSWORD_DEFAULT)]);
    }

}