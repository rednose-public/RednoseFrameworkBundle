<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Cache;

interface CacheInstanceInterface
{
    /**
     * Return the data
     *
     * @return mixed
     */
    public function getData();

    /**
     * Set the data
     *
     * @param mixed $data
     */
    public function setData($data);

    /**
     * Write the data to the filesystem
     */
    public function flush();

    /**
     * Is this instance older then provided modified date
     *
     * @param \DateTime $modified
     *
     * @return boolean
     */
    public function isFresh(\DateTime $modified);

    /**
     * Set the cache modified datetime
     *
     * @param \DateTime $modified
     *
     * @return boolean
     */
    public function setModified(\DateTime $modified);

    /**
     * Get the absolute filePath
     *
     * @return string
     */
    public function getPath();

    /**
     * Get the publicly accessable url
     *
     * @return string
     */
    public function getPublicUrl();

    /**
     * Is this instance available to the webserver
     *
     * @return boolean
     */
    public function isPublic();
}
