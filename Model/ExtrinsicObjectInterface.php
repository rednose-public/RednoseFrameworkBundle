<?php

namespace Rednose\FrameworkBundle\Model;

interface ExtrinsicObjectInterface
{
    /**
     * Returns the id.
     *
     * @return string $id
     */
    function getForeignId();

    /**
     * Sets the id.
     *
     * @param string $id
     */
    function setForeignId($id);
}
