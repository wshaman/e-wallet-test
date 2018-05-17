<?php
/**
    If this error were thrown, someshit has happened
 */

namespace Ewallet\exceptions;



class InternalError extends \Exception
{
    public function __construct($message = "", $code = 0, \Exception $previous = null)
    {
        $message = "Something weird has happened: " . $message;
        return parent::__construct($message, $code, $previous);
    }
}