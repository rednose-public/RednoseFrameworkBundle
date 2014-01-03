<?php

namespace Rednose\FrameworkBundle\Model\Node\Value;

use Rednose\FrameworkBundle\Model\Node\OutputNodeInterface;

/**
 * Output node that receives a value from an input node.
 */
interface OutputValueNodeInterface extends OutputNodeInterface
{
    /**
     * @param string $input
     */
    public function setInputValue($input);
}
