<?php

namespace Rednose\FrameworkBundle\Model;

use Rednose\FrameworkBundle\Exception;

abstract class ExtrinsicObject implements ExtrinsicObjectInterface
{
    protected $foreignId;

    /**
     * Returns the id.
     *
     * @return string $id
     */
    public function getForeignId()
    {
        return $this->foreignId;
    }

    /**
     * Sets and cleans the foreignId.
     *
     * @param string $id
     *
     * @throws \InvalidArgumentException when the id is attempted to be changed.
     */
    public function setForeignId($id)
    {
        if ($this->foreignId !== null || $id === null) {
            throw new \InvalidArgumentException(
                'Error: Invalid value: foreignId is write-once and value can\'t be `null`.'
            );
        }

        $id = preg_replace('/[^\w]/', '_', $id);
        $id = str_replace(' ', '_', $id);

        $this->foreignId = $id;
    }
}
