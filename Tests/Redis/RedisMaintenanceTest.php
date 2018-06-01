<?php

namespace Rednose\FrameworkBundle\Tests\Redis;

use Rednose\FrameworkBundle\Redis\RedisMaintenance;

class RedisMaintenanceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RedisMaintenance
     */
    protected $redisMaintenance;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $connectionMock;

    public function setUp()
    {
        $factoryMock          = $this->getMockBuilder('Rednose\FrameworkBundle\Redis\RedisFactory')->disableOriginalConstructor()->getMock();
        $this->connectionMock = $this->getMockBuilder('Doctrine\DBAL\Connection')->disableOriginalConstructor()->getMock();

        $this->redisMaintenance = new RedisMaintenance($factoryMock, $this->connectionMock);
    }

    public function testProcess()
    {
        $files = [
            __DIR__ . '/../../DataFixtures/Redis/RedisMaintenanceTaskOnce.phps',
            __DIR__ . '/../../DataFixtures/Redis/RedisMaintenanceTask.phps'
        ];

        $this->connectionMock->expects($this->once())->method('executeUpdate')->with(
            'INSERT INTO ' . $this->redisMaintenance::EXECUTED_TABLE_NAME . ' SET taskName = \'RedisMaintenanceTaskOnce\''
        );

        $this->connectionMock->expects($this->exactly(4))->method('fetchColumn')->with(
            $this->logicalOr(
                'SELECT taskName FROM ' . $this->redisMaintenance::EXECUTED_TABLE_NAME,
                'SELECT taskName FROM ' . $this->redisMaintenance::EXECUTED_TABLE_NAME . ' WHERE taskName = \'RedisMaintenanceTaskOnce\''
            )

        )->willReturnCallback(function($where) {
            static $first = true;

            if ($first) {
                if (strpos($where, 'WHERE') > 0) {
                    $first = !$first;
                }

                return false;
            }

            return 'VersionXXXXXXXX';
        });

        $this->assertSame(2, $this->redisMaintenance->process($files));
        $this->assertSame(1, $this->redisMaintenance->process($files));
    }

}