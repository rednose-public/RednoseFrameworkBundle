<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Redis;

use Predis\Client;

/**
 * A Redis service providing a connection client
 */
class RedisFactory
{
    /**
     * @var string
     */
    protected $redisHost;

    /**
     * @var string
     */
    protected $redisAuth;

    /**
     * Redis connection
     *
     * @var \Predis\Client|null
     */
    protected $client = null;

    /**
     * RedisService constructor.
     *
     * @param string             $redisHost
     * @param string             $redisAuth
     */
    public function __construct($redisHost, $redisAuth)
    {
        $this->redisHost = $redisHost;
        $this->redisAuth = $redisAuth;
    }

    /**
     * @return bool
     */
    public function isConfigured()
    {
        return trim($this->redisHost) !== '';
    }

    /**
     * @return \Predis\Client|null
     */
    public function getClient()
    {
        if ($this->isConfigured() === false) {
            return null;
        }

        if ($this->client === null || ($this->client && $this->client->isConnected() === false)) {
            if ($this->client === null) {
                $this->client = new Client( 'tcp://' . $this->redisHost);
            } else {
                $this->client->connect();
            }

            $this->client->auth($this->redisAuth);
        }

        return $this->client;
    }
}