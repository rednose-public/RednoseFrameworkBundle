<?php

namespace Rednose\FrameworkBundle\Cache;

/**
 * Public cache instance
 */
class PublicCacheInstance extends CacheInstance implements CacheInstanceInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPublicUrl()
    {
        return 'TODO';
    }

    /**
     * {@inheritdoc}
     */
    public function isPublic()
    {
        return false;
    }
}
