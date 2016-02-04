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
        $rootPath;
        $cachePath;
        $publicPath;

//        exit;
//        $ca
//        string(47) "/Library/WebServer/Documents/docgen2/app/../web"
//string(68) "/Library/WebServer/Documents/docgen2/app/cache/dev/rednose_framework"
//string(5) "cache"

//        var_dump($rootPath);
//        $this->factory = new CacheFactory();
    }

    public function testCreatePublicCache()
    {
        var_dump(__DIR__);
    }
}