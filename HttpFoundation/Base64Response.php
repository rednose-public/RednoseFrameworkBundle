<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\HttpFoundation;

use Symfony\Component\HttpFoundation\Response;

class Base64Response extends Response
{
    /**
     * Constructor.
     *
     * @param string  $content The response content
     * @param int     $status  The response status code
     * @param array   $headers An array of response headers
     */
    public function __construct($content = '', $status = 200, $headers = array())
    {
        parent::__construct(base64_encode($content), $status, $headers);
    }
}
