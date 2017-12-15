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

use Rednose\FrameworkBundle\Redis\RedisService;

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
     * Session expire in seconds
     *
     * @var int
     */
    private $redisSessionExpire = 0;

    /**
     * @var RedisService
     */
    private $redisService;

    /**
     * RedisSessionHandler constructor.
     *
     * @param RedisService $redisService
     * @param int          $redisSessionExpire
     */
    public function __construct(RedisService $redisService, $redisSessionExpire)
    {
        $this->redisService       = $redisService;
        $this->redisSessionExpire = $redisSessionExpire;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        if ($this->redisService->isConfigured() === false) {
            return parent::close();
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessId)
    {
        if ($this->redisService->isConfigured() === false) {
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
        if ($this->redisService->isConfigured() === false) {
            return parent::gc($maxLifeTime);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function open($savePath, $name)
    {
        if ($this->redisService->isConfigured() === false) {
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
        if ($this->redisService->isConfigured() === false) {
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
        if ($this->redisService->isConfigured() === false) {
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
        return $this->redisService->getClient();
    }
}