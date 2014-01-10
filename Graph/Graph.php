<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <info@rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Graph;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Process\ProcessBuilder;

/**
 * A front-end for feeding .dot-files (GraphViz drawing definitions) to
 * GraphViz's dot tool.
 */
class Graph
{
    private $kernel;

    private $cachePath;

    /**
     * Constructor.
     *
     * @param Kernel $kernel
     */
    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;

        $env  = $kernel->getEnvironment();
        $base = $kernel->getRootDir().'/cache';

        $this->cachePath = sprintf('%s/%s/rednose_framework/graph', $base, $env);
    }

    /**
     * Render a graph
     *
     * @param string $name                The name of the graph to render
     * @param string $type                The type
     * @param mixed  $drawingInstructions Graphviz instructionset
     *
     * @return mixed The rendered image
     */
    public function render($name, $type, $drawingInstructions)
    {
        // FIXME: Caching like this makes no sense as maps differ all the time since processes were introduced in 2.0

        // Flag for checking the validity of existing cache files for this
        // graph.
        $keepExistingCache = false;

        if ($this->isCached($name, 'dot')) {
            if ($this->getCachedFile($name, 'dot') === $drawingInstructions) {
                // We have up-to-date cache files for this graph.
                if ($this->isCached($name, $type)) {
                     // We have a cache, and it is fresh.
                    return $this->getCachedFile($name, $type);
                } else {
                     // We don't have this type of data for this graph, but the
                     // cache we do have are up-to-date and can stay.
                     $keepExistingCache = true;
                }
            }
        }

        // Re-render this graph data.
        $data = $this->callGraphviz($drawingInstructions, $type);

        if (false === $keepExistingCache) {
             // Clear stale cache for this graph.
             $this->clearCache($name);
             // Cache the new drawing instructions.
             $this->cache($name, 'dot', $drawingInstructions);
        }

        // Cache the results of the rendering.
        $this->cache($name, $type, $data);

        return $data;
    }

    /**
     * Render a PNG file of the graph
     *
     * @param string $name                The filename
     * @param array  $drawingInstructions The dot instructions
     *
     * @return mixed The PNG file
     */
    public function renderPng($name, $drawingInstructions)
    {
        return $this->render($name, 'png', $drawingInstructions);
    }

    /**
     * Render an imagemap layover
     *
     * @param string $name                The filename
     * @param array  $drawingInstructions The dot instructions
     *
     * @return mixed The layover
     */
    public function renderImageMap($name, $drawingInstructions)
    {
        return $this->render($name, 'cmapx', $drawingInstructions);
    }

    public function renderPlainMap($name, $drawingInstructions)
    {
        return $this->render($name, 'plain', $drawingInstructions);
    }

    private function callGraphviz($drawingInstructions, $type = 'png')
    {
        $arguments = array(
            'dot',
            '-T'.$type,
        );

        $builder = new ProcessBuilder($arguments);
        $process = $builder->getProcess();

        $process->setStdin($drawingInstructions);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return $process->getOutput();
    }

    private function cachedFileName($name, $extension)
    {
        return $this->cachePath.DIRECTORY_SEPARATOR.$name .'.'.$extension;
    }

    private function isCached($name, $extension)
    {
        if ($this->kernel->getEnvironment() ==='dev') {
            return false;
        }

        return is_writable($this->cachedFileName($name, $extension));
    }

    private function getCachedFile($name, $extension)
    {
        return file_get_contents($this->cachedFileName($name, $extension));
    }

    private function clearCache($name)
    {
        if (is_dir($this->cachePath) !== true) {
            // No such dir yet? Nothing to do for us.
            return;
        }

        $dir = dir($this->cachePath);
        $remove = array();

        // Gather the cache files that match this graph's name.
        while (false !== ($entry = $dir->read())) {
            // Look for the last . in the filename, so `graph.png' is matched for
            // a graph named `graph'.
            if ($name === substr($entry, 0, strrpos($entry, '.'))) {
                $remove[] = $entry;
            }
        }

        // Remove the stale files.
        foreach ($remove as $file) {
            unlink($this->cachePath . DIRECTORY_SEPARATOR . $file);
        }
    }

    private function cache($name, $extension, $data)
    {
        // Write the cache data to file.
        try {
            if (!file_exists($this->cachePath)) {
                // Recursive, `data' should be writable, but we should create
                // the sub-directories.
                mkdir($this->cachePath, 0777, true);
            }
        } catch (\Exception $e) {
            throw new \Exception('Couldn\'t create directory `'.$this->cachePath.'\', is its parent writable?');
        }

        try {
            $fh = fopen($this->cachedFileName($name, $extension), 'w');
            fwrite($fh, $data);
            fclose($fh);
        } catch (\Exception $e) {
            throw new \Exception('Failed to write to GraphViz cache, make sure `'.$this->cachePath.'\' is writable');
        }
    }
}
