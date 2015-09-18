<?php

namespace Rednose\FrameworkBundle\Cache;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Filesystem cache factory service
 */
class CacheFactory implements CacheFactoryInterface
{
    /**
     * @var string
     */
    protected $rootPath;

    /**
     * @var string
     */
    protected $cachePath;

    /**
     * @var string
     */
    protected $publicPath;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Service constructor
     *
     * @param string       $rootPath   The web root path.
     * @param string       $cachePath  The absolute cache path.
     * @param string       $publicPath The relative cache path for publicly accessible files (via the webserver).
     * @param ContainerInterface $container
     */
    public function __construct($rootPath, $cachePath, $publicPath, ContainerInterface $container)
    {
        $this->rootPath   = $rootPath;
        $this->cachePath  = $cachePath;
        $this->publicPath = $publicPath;
        $this->container  = $container;
    }

    /**
     * Create a new caching instance
     *
     * @param string  $filePath
     * @param boolean $public
     *
     * @return CacheInstanceInterface
     */
    public function create($filePath, $public = false)
    {
        $helper = $this->container->get('templating.helper.assets');

        if ($public) {
            return new PublicCacheInstance($filePath, $helper->getUrl($this->publicPath), $this->rootPath);
        }

        return new CacheInstance($this->cachePath, $filePath);
    }
}
