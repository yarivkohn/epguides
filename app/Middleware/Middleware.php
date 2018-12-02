<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/18/18
 * Time: 10:24 PM
 */

namespace Epguides\Middleware;

class Middleware
{
    protected $_container;


    public function __construct($container)
    {
        $this->_container = $container;
    }
}