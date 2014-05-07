<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
