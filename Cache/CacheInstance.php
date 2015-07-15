<?php

namespace Rednose\FrameworkBundle\Cache;

/**
 * Private cache instance
 */
class CacheInstance implements CacheInstanceInterface
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var \DateTime
     */
    protected $dateTime;

    /**
     * @var string
     */
    protected $data;

    /**
     * @var string
     */
    protected $dataType;

    public function __construct($cachePath)
    {
        $this->path = $cachePath;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        if (!$this->dateTime || !$this->dataType) {
            $this->loadMeta();
        }

        if ($this->data === null) {
            if (@$data = file_get_contents($this->path)) {
                $this->data = $data;
            } else {
                throw new \RuntimeException(sprintf('Unable to read cache %s', $this->path));
            }
        }

        if ($this->dataType === 'boolean') {
            return (bool)($this->data === '1');
        }

        if ($this->dataType === 'array' || $this->dataType === 'object') {
            return unserialize($this->data);
        }

        if ($this->dataType === 'DOMDocument') {
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $dom->loadXML($this->data);

            return $dom;
        }

        return $this->data;
    }

    public function setData($data)
    {
        if (is_string($data)) {
            $this->data     = $data;
            $this->dataType = 'string';
        } elseif (is_bool($data)) {
            $this->data     = $data ? '1': '0';
            $this->dataType = 'boolean';
        } elseif (is_array($data)) {
            $this->data     = serialize($data);
            $this->dataType = 'array';
        } elseif ($data instanceOf \DOMDocument) {
            $this->data     = $data->saveXML();
            $this->dataType = 'DOMDocument';
        } elseif (is_object($data)) {
            $this->data     = serialize($data);
            $this->dataType = 'object';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $dir      = dirname($this->path);
        $fileName = basename($this->path);

        if (is_dir($dir) === false) {
            if (@mkdir($dir, 0777, true) === false) {
                throw new \RuntimeException(sprintf('Unable to create the %s directory', $dir));
            }
        } elseif (is_writable($dir) === false) {
            throw new \RuntimeException(sprintf('Unable to write in the %s directory', $dir));
        }

        if (@file_put_contents($dir . '/' . $fileName, $this->data) !== false) {
            @chmod($dir . '/' . $fileName, 0666 & ~umask());
        } else {
            throw new \RuntimeException(sprintf('Failed to write cache file "%s".', $dir . '/' . $fileName));
        }

        if (!$this->dateTime) {
            $this->dateTime = new \DateTime();
        }

        $meta = serialize(array(
            'modified' => $this->dateTime->format(\DateTime::ISO8601),
            'dataType' => $this->dataType
        ));
        $metaFile = $dir . '/' . $fileName . '.meta';

        if (@file_put_contents($metaFile, $meta)) {
            @chmod($metaFile, 0666 & ~umask());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh(\DateTime $modified)
    {
        if (!$this->dateTime) {
            $this->loadMeta();

            if (!$this->dateTime) {
                return false;
            }
        }

        return ($modified->getTimestamp() <= $this->dateTime->getTimestamp());
    }

    /**
     * {@inheritdoc}
     */
    public function setModified(\DateTime $modified)
    {
        $this->dateTime = $modified;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicUrl()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isPublic()
    {
        return false;
    }

    /**
     * Load the .meta file
     */
    private function loadMeta()
    {
        $dir      = dirname($this->path);
        $fileName = basename($this->path);

        $metaFile = $dir . '/' . $fileName . '.meta';

        if (file_exists($metaFile) === true) {
            if (@$metaData = file_get_contents($metaFile)) {
                $meta = @unserialize($metaData);

                if (isset($meta['modified']) === false || isset($meta['dataType']) === false) {
                    throw new \RuntimeException(sprintf('Invalid meta file for %s.meta', $this->path));
                }

                $this->dataType = $meta['dataType'];
                $this->dateTime = new \DateTime($meta['modified']);
            }
        }
    }
}
