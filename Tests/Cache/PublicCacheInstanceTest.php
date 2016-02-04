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
        $publicPath = '/cache';
        $rootPath = '/../Fixtures/web';

        $this->cache = new PublicCacheInstance($cacheName, $publicPath, $rootPath);
    }

    public function testCreateCache()
    {
    }

    public function testIsFresh()
    {
    }

    public function publicUrl()
    {
        $this->assertEquals('/cache/doctanium_asset/thumbnail/232.png', $this->cache->getPublicUrl());
    }
}
