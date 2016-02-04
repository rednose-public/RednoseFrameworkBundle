<?php

namespace Rednose\FrameworkBundle\Cache;

/**
 * Public cache instance
 */
class PublicCacheInstance extends CacheInstance implements CacheInstanceInterface
{
    /**
     * @var string
     */
    protected $cacheName;

    /**
     * @var string
     */
    protected $publicPath;

    /**
     * Constructor
     *
     * @param string $cacheName
     * @param string $publicPath
     * @param string $rootPath
     */
    public function __construct($cacheName, $publicPath, $rootPath)
    {
        var_dump($cacheName);
        var_dump($publicPath);
        var_dump($rootPath);
        exit;

        $this->cacheName  = $cacheName;
        $this->publicPath = $publicPath;

        parent::__construct($rootPath.'/cache', $cacheName);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicUrl()
    {
        return $this->publicPath.'/'.$this->cacheName;
    }

    /**
     * {@inheritdoc}
     */
    public function isPublic()
    {
        return false;
    }
}
