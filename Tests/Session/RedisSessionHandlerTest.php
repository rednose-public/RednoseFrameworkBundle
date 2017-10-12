<?php

namespace Rednose\FrameworkBundle\Tests\Model;

use Rednose\FrameworkBundle\Session\Redis\RedisSessionHandler;

class RedisSessionHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $predisClientMock;

    /**
     * @var RedisSessionHandler
     */
    private $sessionHandler;

    public function testRedisSessionHandlerOpen()
    {
        $this->createHandler();
        $this->predisClientMock->expects($this->once())->method('auth')->with('p4ssw0rd')->willReturn(true);

        $this->sessionHandler->open('/tmp', 'PHPSESSID');
    }

    public function testRedisSessionHandlerWrite()
    {
        $this->createHandler();
        $this->predisClientMock->expects($this->once())->method('set')->with('REDNOSE_SESS_sessionId', '[]', 'EX', 172800)->willReturn(true);

        $this->sessionHandler->write('sessionId', '[]');
    }

    public function testRedisSessionHandlerRead()
    {
        $this->createHandler();
        $this->predisClientMock->expects($this->once())->method('get')->with('REDNOSE_SESS_sessionId')->willReturn([]);

        $this->sessionHandler->read('sessionId');
    }

    private function createHandler()
    {
        $this->predisClientMock = $this->getMockBuilder('\Predis\Client')->disableOriginalConstructor();

        // Predis uses magic function calls... (urght...)...
        $this->predisClientMock->setMethods(['auth', 'set', 'get']);
        $this->predisClientMock = $this->predisClientMock->getMock();

        $factoryMock = $this->getMock('Rednose\FrameworkBundle\Session\Redis\RedisPredisFactory');
        $factoryMock->expects($this->once())->method('create')->with('tcp://localhost:6379')->willReturn($this->predisClientMock);

        $this->sessionHandler = new RedisSessionHandler($factoryMock, 'localhost:6379', 'p4ssw0rd', 172800);
    }
}