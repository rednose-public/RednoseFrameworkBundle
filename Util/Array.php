<?php

namespace Rednose\FrameworkBundle\Util;

/**
 * Several array utility methods.
 */
class ArrayUtil
{
    /**
     * Checks whether a given path exists within an array, ie. "Address.Street".
     *
     * @param array $arr
     * @param string $path
     *
     * @return mixed
     */
    public static function has(array $arr, $path)
    {
        if (!$path) {
            return null;
        }

        $segments = is_array($path) ? $path : explode('.', $path);

        $cur = &$arr;

        foreach ($segments as $segment) {
            if (!array_key_exists($segment, $cur)) {
                return false;
            }

            $cur = $cur[$segment];
        }

        return true;
    }

    /**
     * Gets a value from an array by specifying a path, ie. "Address.Street".
     *
     * @param array $arr
     * @param string $path
     *
     * @return mixed
     */
    public static function get(array $arr, $path)
    {
        if (!$path) {
            return null;
        }

        $segments = is_array($path) ? $path : explode('.', $path);

        $cur = &$arr;

        foreach ($segments as $segment) {
            if (!isset($cur[$segment])) {
                return null;
            }

            $cur = $cur[$segment];
        }

        return $cur;
    }

    /**
     * Sets a value within an array by specifying a path, ie. "Address.Street".
     *
     * @param array  $arr
     * @param string $path
     * @param mixed  $value
     */
    public static function set(array &$arr, $path, $value)
    {
        if (!$path) {
            return;
        }

        $segments = is_array($path) ? $path : explode('.', $path);

        $cur = &$arr;

        foreach ($segments as $segment) {
            if (!isset($cur[$segment])) {
                $cur[$segment] = array();
            }

            $cur = &$cur[$segment];
        }

        $cur = $value;
    }
}
