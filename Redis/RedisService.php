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

/**
 * A Redis service providing a connection client
 */
class RedisService
{
    /**
     * @var RedisPredisFactory
     */
    protected $factory;

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
     * @param RedisPredisFactory $predisFactory
     * @param string             $redisHost
     * @param string             $redisAuth
     */
    public function __construct(RedisPredisFactory $predisFactory, $redisHost, $redisAuth)
    {
        $this->factory   = $predisFactory;
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
     * @return \Predis\Client|bool
     */
    public function getClient()
    {
        if ($this->client === null) {
            $this->client = $this->factory->create( 'tcp://' . $this->redisHost);
            $this->client->auth($this->redisAuth);
        }

        return $this->client;
    }
}