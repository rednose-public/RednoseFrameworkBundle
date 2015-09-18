<?php

namespace Rednose\FrameworkBundle\Cache;

/**
 * Filesystem cache factory service
 */
interface CacheFactoryInterface
{
    /**
     * Create a new caching instance
     *
     * @param string $filePath
     * @param boolean $public
     *
     * @return CacheInstanceInterface
     */
    public function create($filePath, $public = false);
}