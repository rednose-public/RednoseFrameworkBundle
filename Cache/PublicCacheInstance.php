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
        $this->cacheName  = $cacheName;
        $this->publicPath = $publicPath;

        parent::__construct($rootPath.$publicPath, $cacheName);
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
