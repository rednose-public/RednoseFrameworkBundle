<?php

namespace Rednose\FrameworkBundle\Cache;
use Symfony\Component\Routing\RouterInterface;

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
     * @var RouterInterface
     */
    protected $router;

    /**
     * Service constructor
     *
     * @param string $rootPath          The application root path.
     * @param string $cachePath         The absolute cache path.
     * @param string $publicPath        The relative cache path for publicly accessable files (via the webserver).
     * @param routerInterface $router   The Router
     */
    public function __construct($rootPath, $cachePath, $publicPath, RouterInterface $router)
    {
        $rootPath = explode('/', $rootPath);
        array_pop($rootPath);

        $this->rootPath   = join('/', $rootPath);
        $this->cachePath  = $cachePath;
        $this->publicPath = $publicPath;
        $this->router     = $router;
    }

    /**
     * Create a new caching instance
     *
     * @param string $filePath
     * @param boolean $public
     *
     * @return CacheInstanceInterface
     */
    public function create($filePath, $public = false)
    {
        if ($public) {
            return new PublicCacheInstance($filePath, $this->publicPath, $this->rootPath, $this->router);
        }

        return new CacheInstance($this->cachePath, $filePath);
    }
}
