<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Model;

class PrioritizedArray implements \Iterator, \ArrayAccess
{
    protected $offset;

    protected $keyName = '';
    protected $data = [];
    protected $priorities = [];

    public function __construct($keyName)
    {
        $this->keyName = $keyName;
        $this->offset  = 0;
    }

    public function loadArray($array)
    {
        $this->clear();

        if (!is_array($array)) {
            return;
        }

        if (count($array) !== 2) {
            return;
        }

        $first = current($array);
        next($array);
        $second = current($array);

        if (isset($first[0]) && $first[0] === 'priority') {
            $this->data = $second[1];
            $this->priorities = $first[1];
        }

        if (isset($second[0]) && $second[0] === 'priority') {
            $this->data = $first[1];
            $this->priorities = $second[1];
        }

        $this->reIndex();
    }

    public function load($data)
    {
        if (is_array($data) === false) {
            $data = @unserialize($data);
        }

        $this->loadArray($data);
    }

    public function clear()
    {
        $this->data = [];
        $this->priorities = [];
    }

    public function addElement($data, $priority)
    {
        $this->data[]       = $data;
        $this->priorities[] = $priority;

        $this->reIndex();
    }

    public function removeElement($data)
    {
        foreach ($this->data as $offset => $dataItem) {
            if ($dataItem === $data) {
                unset($this->data[$offset]);
                unset($this->priorities[$offset]);
            }
        }

        $this->reIndex();
    }

    public function rewind()
    {
        $this->offset = 0;
    }

    public function current()
    {
        return [
            $this->keyName => $this->data[$this->offset],
            'priority'     => $this->priorities[$this->offset]
        ];
    }

    public function key()
    {
        return $this->offset;
    }

    public function next()
    {
        ++$this->offset;
    }

    public function valid()
    {
        return isset($this->data[$this->offset]);
    }

    public function __toString()
    {
        $this->reIndex();

        return serialize([
            [$this->keyName, $this->data],
            ['priority', $this->priorities]
        ]);
    }

    public function offsetGet($offset)
    {
        $this->offsetValid($offset);

        if (substr($offset, 0, 9) === 'priority_') {
            $offset = str_replace('priority_', '', $offset);
            $offset = (int)$offset;

            return $this->priorities[$offset];
        }
        $offset = str_replace($this->keyName . '_', '', $offset);
        $offset = (int)$offset;

        return $this->data[$offset];
    }

    public function offsetExists($offset)
    {
        $this->offsetValid($offset);

        if (substr($offset, 0, 9) === 'priority_') {
            $offset = str_replace('priority_', '', $offset);
            $offset = (int)$offset;

            return isset($this->priorities[$offset]);
        }

        $offset = str_replace($this->keyName . '_', '', $offset);
        $offset = (int)$offset;

        return isset($this->data[$offset]);
    }

    public function offsetSet($offset, $value)
    {
        $this->offsetValid($offset);

        if (substr($offset, 0, 9) === 'priority_') {
            $offset = str_replace('priority_', '', $offset);
            $offset = (int)$offset;

            $this->priorities[$offset] = $value;

            $this->reIndex();

            return;
        }

        $offset = str_replace($this->keyName . '_', '', $offset);
        $offset = (int)$offset;

        $this->data[$offset] = $value;

        $this->reIndex();
    }

    public function offsetUnset($offset)
    {
        throw new \Exception('Not implemented');
    }

    protected function offsetValid($offset)
    {
        if (substr($offset, 0, 9) !== 'priority_' && substr($offset, 0, strlen($this->keyName)) !== $this->keyName) {
            throw new \Exception('Invalid key name (expected: ' . $this->keyName . ' or priority)');
        }
    }

    protected function reIndex()
    {
        $data = [];
        $priorities = [];

        foreach ($this->data as $dataItem) {
            $data[] = $dataItem;
        }

        foreach ($this->priorities as $priority) {
            $priorities[] = $priority;
        }

        $this->data = $data;
        $this->priorities = $priorities;
    }
}
