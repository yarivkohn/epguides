<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/19/18
 * Time: 7:58 AM
 */

namespace Epguides\Validation\Rules;

use Epguides\Models\User;
use Respect\Validation\Rules\AbstractRule;
use Slim\Handlers\AbstractError;

class EmailAvailable extends AbstractRule
{
    public function validate($input)
    {
      return User::where('email', $input)->count() === 0;
    }

}