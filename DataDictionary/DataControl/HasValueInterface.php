<?php

namespace Rednose\FrameworkBundle\DataDictionary\DataControl;

interface HasValueInterface
{
    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @param mixed $value
     */
    public function setValue($value);
}
