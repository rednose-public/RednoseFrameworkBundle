<?php

namespace Rednose\FrameworkBundle\Tests\Session;

use Rednose\FrameworkBundle\Redis\RedisFactory;
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
        $this->predisClientMock->setMethods(['auth', 'set', 'get', 'isConnected', 'connect']);
        $this->predisClientMock = $this->predisClientMock->getMock();
        $this->predisClientMock->expects($this->once())->method('isConnected')->willReturn(false);

        $redisFactory = new RedisFactory('localhost:6379', 'p4ssw0rd');

        $reflect = new \ReflectionProperty(get_class($redisFactory), 'client');
        $reflect->setAccessible(true);
        $reflect->setValue($redisFactory, $this->predisClientMock);
        $reflect->setAccessible(false);

        $this->sessionHandler = new RedisSessionHandler($redisFactory, 172800);
    }
}