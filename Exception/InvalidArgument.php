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

use Rednose\FrameworkBundle\Exception\Exception;

/**
 * Thrown when a function expects a specific type of argument, but got
 * something else.
 */
class InvalidArgument extends Exception
{
}
