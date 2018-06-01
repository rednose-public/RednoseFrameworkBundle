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
 * Redis maintenance task definition interface
 */
interface RedisMaintenanceTaskInterface
{
    /**
     * Execute the task
     *
     * @var RedisFactory $factory
     */
    public function up(RedisFactory $factory);

    /**
     * Should this task run once or every time the command is called
     *
     * @return bool
     */
    public function runOnce();
}