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
 * Redis maintenance class processor
 */
class RedisMaintenance
{
    /**
     * @var RedisFactory
     */
    protected $factory;

    /**
     * RedisMaintenance constructor
     *
     * @param RedisFactory $factory
     */
    public function __construct(RedisFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Process all provided classes.
     *
     * If a class is runOnce it will be skipped if its already marked as executed
     *
     * @param array $files
     */
    public function process(array $files)
    {
        print_R($files);

        return;
    }

}