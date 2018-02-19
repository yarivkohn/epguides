<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/19/18
 * Time: 7:58 AM
 */

namespace Epguides\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class EmailAvailableException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'Email address already exists',
        ],
    ];
}