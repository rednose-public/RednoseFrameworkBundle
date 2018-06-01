<?php

namespace Rednose\FrameworkBundle\DataFixtures\Redis;

use Rednose\FrameworkBundle\Redis\RedisFactory;
use Rednose\FrameworkBundle\Redis\RedisMaintenanceTaskInterface;

class RedisMaintenanceTask implements RedisMaintenanceTaskInterface
{
    /**
     * {@inheritdoc}
     */
    public function up(RedisFactory $factory)
    {
        $redis = $factory->getClient();

        /** Your implementation here */
    }

    /**
     * {@inheritdoc}
     */
    public function runOnce()
    {
        return false;
    }
}