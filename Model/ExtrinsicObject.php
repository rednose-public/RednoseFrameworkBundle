<?php

namespace Rednose\FrameworkBundle\Model;

use Rednose\FrameworkBundle\Exception;

abstract class ExtrinsicObject implements ExtrinsicObjectInterface
{
    /**
     * Returns the id.
     *
     * @return string $id
     */
    final public function getForeignId()
    {
        return $this->foreignId;
    }

    /**
     * Sets and cleans the foreignId.
     *
     * @param string $id
     */
    final public function setForeignId($id)
    {
        if ($this->id !== null || $id === null) {
            throw new Exception\InvalidArgument(
                'Error: Invalid value: foreignId is write-once and value can\'t be `null`.'
            );
        }

        $id = preg_replace('/[^\w]/', '_', $id);
        $id = str_replace(' ', '_', $id);

        $this->foreignId = $id;
    }
}
