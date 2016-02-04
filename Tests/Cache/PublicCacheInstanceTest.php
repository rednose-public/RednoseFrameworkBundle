<?php

namespace Rednose\FrameworkBundle\Tests\Util;

use Rednose\FrameworkBundle\Cache\PublicCacheInstance;

class PublicCacheInstanceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PublicCacheInstance
     */
    protected $cache;

    public function setUp()
    {
        $cacheName = 'doctanium_asset/thumbnail/232.png';
        $publicPath = '/cache/test';
        $rootPath = __DIR__.'/../Fixtures/web';

        $this->cache = new PublicCacheInstance($cacheName, $publicPath, $rootPath);
    }

    public function testFlush()
    {
        $this->cache->setData('test');
        $this->cache->flush();

        $this->assertFileExists(__DIR__.'/../Fixtures/web/cache/test/doctanium_asset/thumbnail/232.png');
    }

    public function testPublicUrl()
    {
        $this->assertEquals('/cache/test/doctanium_asset/thumbnail/232.png', $this->cache->getPublicUrl());
    }
}
