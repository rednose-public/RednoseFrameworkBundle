<?php

/*
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Thumbnail;

use Rednose\FrameworkBundle\Cache\CacheFactoryInterface;
use Rednose\FrameworkBundle\Model\FileInterface;

class ThumbnailGenerator
{
    /**
     * @var CacheFactoryInterface
     */
    protected $cacheFactory;

    /**
     * @param CacheFactoryInterface $cacheFactory
     */
    public function __construct(CacheFactoryInterface $cacheFactory)
    {
        $this->cacheFactory = $cacheFactory;
    }

    /**
     * @param FileInterface $file
     *
     * @return string
     */
    public function getFileUrl(FileInterface $file)
    {
        $path = sprintf('doctanium_asset/thumbnail/%s.%s', $file->getId(), $this->getExtension($file));

        var_dump($path);
        exit;
        $cache = $this->cacheFactory->create($path, true);

        if ($cache->isFresh($file->getDateModified()) === false) {
            $cache->setData($file->getContent());
            $cache->flush();
        }

        return $cache->getPublicUrl();
    }

    /**
     * @param FileInterface $file
     *
     * @return string
     */
    protected function getExtension(FileInterface $file)
    {
        $parts = explode('/', $file->getMimeType());

        return $parts[1];
    }
}
