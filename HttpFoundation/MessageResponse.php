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

use Rednose\FrameworkBundle\Message\Message;
use Symfony\Component\HttpFoundation\JsonResponse;

class MessageResponse extends JsonResponse
{
    /**
     * Constructor.
     *
     * @param Message|Message[] $messages
     * @param int               $status  The response status code
     * @param array             $headers An array of response headers
     */
    public function __construct($messages, $status = 200, $headers = array())
    {
        if (!is_array($messages)) {
            $messages = array($messages);
        }

        if (empty($messages)) {
            throw new \LogicException('The messages must not be empty.');
        }

        parent::__construct(array('messages' => $messages), 200, $headers);
    }
}
