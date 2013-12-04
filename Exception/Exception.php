<?php

namespace Rednose\FrameworkBundle\Exception;

/**
 * Basic exception.
 */
class Exception extends \Exception
{
    /**
     * Constructor
     *
     * @param string     $message  Exception message
     * @param integer    $code     Error code
     * @param \Exception $previous The previous exception
     */
    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
