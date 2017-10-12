<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Session;

/**
 * Support storing sessions in a centralized redis.
 *
 * To use this service configure redis_host and redis_auth in parameters.yml
 *
 * If redis is not configured it will fallback to the native php session handler
 */
class RedisSessionHandler extends \SessionHandler implements \SessionHandlerInterface
{
    private $redisActivated = false;

    private $redisHost = '';

    private $redisAuth = '';

    /**
     * RedisSessionHandler constructor.
     *
     * @param string $redisHost
     * @param string $redisAuth
     */
    public function __construct($redisHost, $redisAuth)
    {
        $this->redisActivated = ($redisHost !== '');
        $this->redisHost = $redisHost;
        $this->redisAuth = $redisAuth;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        if ($this->redisActivated === false) {
            return parent::close();
        }

    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessId)
    {
        if ($this->redisActivated === false) {
            return parent::destroy($sessId);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function gc($maxLifeTime)
    {
        if ($this->redisActivated === false) {
            return parent::gc($maxLifeTime);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function open($savePath, $name)
    {
        $name = 'REDNOSE_SESS_';

        if ($this->redisActivated === false) {
            return parent::open($savePath, $name);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessId)
    {
        if ($this->redisActivated === false) {
            return parent::read($sessId);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function write($sessId, $data)
    {
        if ($this->redisActivated === false) {
            return parent::write($sessId, $data);
        }
    }
}