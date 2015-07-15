<?php

namespace Rednose\FrameworkBundle\Cache;

/**
 * Filesystem cache factory service
 */
class CacheFactory
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $publicPath;

    /**
     * Service constructor
     *
     * @param string $path The absolute cache path.
     * @param string $publicPath The absolute cache path for publicly accessable files (via the webserver).
     */
    public function __construct($path, $publicPath)
    {
        $this->path = $path;
        $this->publicPath = $publicPath;
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
            return new PublicCacheInstance($this->publicPath . '/' . $filePath);
        }

        return new CacheInstance($this->path . '/' . $filePath);
    }
}
