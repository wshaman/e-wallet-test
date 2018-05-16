<?php
/**
 * Created by PhpStorm.
 * User: wshaman
 * Date: 14.11.16
 * Time: 17:06
 */

namespace Engine\exceptions;


class NoDBConnectedException extends  \Exception
{
    public function __construct($message = "Database connection failed", $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
