<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/18/18
 * Time: 6:10 PM
 */

namespace Epguides\Validation;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as Respect;

class Validator
{

    protected $_errors;

    public function validator($request, array $rules)
    {
        foreach($rules as $field => $rule){
            try{
                $rule->setName(ucfirst($field))->assert($request->getParam($field));
            } catch (NestedValidationException $e){
                $this->_errors[$field] = $e->getMessages();
            }
        }

        $_SESSION['errors'] = $this->_errors;

        return $this;
    }

    public function failed()
    {
        return !empty($this->_errors);
    }

}