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

class MatchesPassword extends AbstractRule
{

    protected $_password;

    public function __construct($password)
    {
        $this->_password = $password;
    }

    public function validate($input)
    {
        return password_verify($input, $this->_password);
    }
}