<?php

namespace Rednose\FrameworkBundle\Tests\Util;

use Rednose\FrameworkBundle\Cache\CacheFactory;

class CacheFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CacheFactory
     */
    protected $factory;

    public function setUp()
    {
        $rootPath = __DIR__.'/../Fixtures/web';
        $cachePath = __DIR__.'/../Fixtures/app/cache/dev/rednose_framework';
        $publicPath = 'cache';

        $helper = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Templating\Helper\AssetsHelper')
            ->disableOriginalConstructor()
            ->getMock();

        $helper->expects($this->any())
            ->method('getUrl')
            ->will($this->returnCallback(function($path) {
                return '/'.$path;
            }));

        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

        $container->expects($this->any())
            ->method('get')
            ->will($this->returnValue($helper));

        $this->factory = new CacheFactory($rootPath, $cachePath, $publicPath, $container);
    }

    public function testCreatePublicCache()
    {
        $cache = $this->factory->create('doctanium_asset/thumbnail/232.png', true);

        $this->assertInstanceOf('Rednose\FrameworkBundle\Cache\PublicCacheInstance', $cache);
        $this->assertEquals('/cache/doctanium_asset/thumbnail/232.png', $cache->getPublicUrl());
    }
}