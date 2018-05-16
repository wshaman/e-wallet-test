<?php
/**
 * Created by PhpStorm.
 * User: wshaman
 * Date: 15.11.16
 * Time: 11:32
 */

namespace Engine\exceptions;


use Engine\tools\C;

class WrongParamException extends \Exception
{
    public function __construct($message = "", $code = 0, \Exception $previous = null)
    {
        if($code===0) $code = C::ERROR_CODE_WRONG_PARAMS;
        return parent::__construct($message, $code, $previous);
    }
}
