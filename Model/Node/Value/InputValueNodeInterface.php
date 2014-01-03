<?php

namespace Rednose\FrameworkBundle\Model\Node\Value;

use Rednose\FrameworkBundle\Model\Node\InputNodeInterface;

/**
 * Input node that supplies a value to an output node.
 */
interface InputValueNodeInterface extends InputNodeInterface
{
    /**
     * @return string
     */
    public function getOutputValue();
}
