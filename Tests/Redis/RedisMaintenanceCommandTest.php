<?php

namespace Rednose\FrameworkBundle\Tests\Redis;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class RedisMaintenanceCommandTest extends \PHPUnit_Framework_TestCase
{
    protected $application;

    protected $path;

    public function setUp()
    {
        $kernel = new \AppKernel('test', true);
        $kernel->boot();

        $this->application = new Application($kernel);

        $this->path = $kernel->getContainer()->getParameter('rednose_framework.redis.maintenance_path');
    }

    public function testExecuteCommand()
    {
        $command = $this->application->find('rednose:framework:redis-execute');

        touch($this->path . '/Version1234.php');

        $this->expectExceptionMessage('Unable to determine namespace of class ' . $this->path . '/Version1234.php');

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        unlink($this->path . '/Version1234.php');
    }

    public function testGenerateCommand()
    {
        $command = $this->application->find('rednose:framework:redis-generate');

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/Generated new maintenance class to ".*Version.*.php"/i', $output);

        $fileName = [];
        $className = [];

        preg_match('/Generated new maintenance class to "(.*Version.*.php)"/i', $output, $fileName);
        preg_match('/Generated new maintenance class to ".*(Version.*).php"/i', $output, $className);

        $this->assertContains($this->path, $output);

        // Test php syntax validity of generated class
        require($fileName[1]);

        // Cleanup
        unlink($fileName[1]);

        $className[1] = '\Rednose\FrameworkBundle\Redis\Maintenance\\' . $className[1];

        $generatedClass = new $className[1]();

        $this->assertTrue($generatedClass->runOnce());
    }
}