<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Session\Redis;

/**
 * Factory service for creating the Predis\Client
 */
class RedisPredisFactory
{
    /**
     * @param $host
     * @return \Predis\Client
     */
    public function create($host)
    {
        return new \Predis\Client($host);
    }
}