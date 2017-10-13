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
 * Support storing sessions in a centralized redis.
 *
 * To use this service configure redis_host and redis_auth in parameters.yml
 *
 * If redis is not configured it will fallback to the native php session handler
 */
class RedisSessionHandler extends \SessionHandler implements \SessionHandlerInterface
{
    /**
     * Redis key prefix
     */
    const PREFIX = 'REDNOSE_SESS_';

    /**
     * @var bool
     */
    private $redisActivated = false;

    /**
     * @var RedisPredisFactory
     */
    private $factory;

    /**
     * Expected pattern is host:port
     *
     * @var string
     */
    private $redisHost = '';

    /**
     * @var string
     */
    private $redisAuth = '';

    /**
     * Session expire in seconds
     *
     * @var int
     */
    private $redisSessionExpire = 0;

    /**
     * RedisSessionHandler constructor
     *
     * @param RedisPredisFactory $predisFactory
     * @param string             $redisHost
     * @param string             $redisAuth
     * @param int                $redisSessionExpire
     */
    public function __construct($predisFactory, $redisHost, $redisAuth, $redisSessionExpire)
    {
        $this->redisActivated     = (bool)$redisHost;
        $this->factory            = $predisFactory;
        $this->redisHost          = $redisHost;
        $this->redisAuth          = $redisAuth;
        $this->redisSessionExpire = $redisSessionExpire;
        die(var_dump($this->redisActivated));
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        if ($this->redisActivated === false) {
            return parent::close();
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessId)
    {
        if ($this->redisActivated === false) {
            return parent::destroy($sessId);
        }

        $redisClient = $this->getClient();

        return $redisClient->del([ $this::PREFIX . $sessId ]);
    }

    /**
     * {@inheritdoc}
     */
    public function gc($maxLifeTime)
    {
        if ($this->redisActivated === false) {
            return parent::gc($maxLifeTime);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function open($savePath, $name)
    {
        if ($this->redisActivated === false) {
            return parent::open($savePath, $name);
        }

        // Check if the connection can be established
        $this->getClient();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessId)
    {
        if ($this->redisActivated === false) {
            return parent::read($sessId);
        }

        $redisClient = $this->getClient();

        return $redisClient->get($this::PREFIX . $sessId);
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessId, $data)
    {
        if ($this->redisActivated === false) {
            return parent::write($sessId, $data);
        }

        $redisClient = $this->getClient();

        return $redisClient->set($this::PREFIX . $sessId, $data,"EX", $this->redisSessionExpire);
    }

    /**
     * @return \Predis\Client
     */
    protected function getClient()
    {
        try {
            $client = $this->factory->create( 'tcp://' . $this->redisHost);
            $client->auth($this->redisAuth);
        } catch (\Exception $e) {
            // At this stage (session-open) exceptions are not catchable by the framework
            // So lets do it the old-skool way.
            die($e->getMessage());
        }

        return $client;
    }
}