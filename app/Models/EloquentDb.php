<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/21/18
 * Time: 9:57 AM
 */

namespace Epguides\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;

class EloquentDb extends EloquentModel
{
	public static function beginTransaction()
	{
		self::getConnectionResolver()->connection()->beginTransaction();
	}

	public static function commit()
	{
		self::getConnectionResolver()->connection()->commit();
	}

	public static function rollback()
	{
		self::getConnectionResolver()->connection()->rollBack();
	}
}