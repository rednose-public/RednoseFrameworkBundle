<?php

namespace Doctanium\Bundle\GeneratorBundle\Generator;

/**
 * Enables caching of files created within document builders.
 */
class BinaryCache
{
    private $file;

    /**
     * Constructor.
     *
     * @param string $file The absolute cache path.
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * Gets the cache file path.
     *
     * @return string The cache file path.
     */
    public function __toString()
    {
        return $this->file;
    }

    /**
     * Checks if the cache is still fresh.
     *
     * @param string $md5 The hash of the source content to check.
     *
     * @return bool true if the cache is fresh, false otherwise.
     */
    public function isFresh($md5)
    {
        if (!is_file($this->file)) {
            return false;
        }

        $metadata = $this->file.'.meta';
        if (!is_file($metadata)) {
            return false;
        }

        return $md5 === file_get_contents($metadata);
    }

    /**
     * Writes cache.
     *
     * @param string $content The content to write in the cache.
     * @param string $md5     Hash of the source content.
     *
     * @throws \RuntimeException When cache file can't be written.
     */
    public function write($content, $md5)
    {
        $dir = dirname($this->file);
        if (!is_dir($dir)) {
            if (false === @mkdir($dir, 0777, true)) {
                throw new \RuntimeException(sprintf('Unable to create the %s directory', $dir));
            }
        } elseif (!is_writable($dir)) {
            throw new \RuntimeException(sprintf('Unable to write in the %s directory', $dir));
        }

        $tmpFile = tempnam(dirname($this->file), basename($this->file));
        if (false !== @file_put_contents($tmpFile, $content) && @rename($tmpFile, $this->file)) {
            @chmod($this->file, 0666 & ~umask());
        } else {
            throw new \RuntimeException(sprintf('Failed to write cache file "%s".', $this->file));
        }

        $file = $this->file.'.meta';
        $tmpFile = tempnam(dirname($file), basename($file));
        if (false !== @file_put_contents($tmpFile, $md5) && @rename($tmpFile, $file)) {
            @chmod($file, 0666 & ~umask());
        }
    }
}
