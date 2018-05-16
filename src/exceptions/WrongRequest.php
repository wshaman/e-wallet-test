<?php
/**
 * Created by PhpStorm.
 * User: wshaman
 * Date: 5/16/18
 * Time: 11:48 PM
 */

namespace Ewallet\exceptions;


use Engine\tools\C;

class WrongRequest extends \Exception
{
    public function __construct($message = "", $code = 0, \Exception $previous = null)
    {
        $message = "Wrong request received: " . $message;
        return parent::__construct($message, $code, $previous);
    }
}