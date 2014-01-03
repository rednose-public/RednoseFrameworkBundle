<?php

namespace Rednose\FrameworkBundle\Model\Node;

/**
 * A node within a graph. Currently limited to single input and output.
 */
interface InputNodeInterface
{
    /**
     * @return OutputNodeInterface
     */
    public function getOutput();

    /**
     * @param OutputNodeInterface $node
     */
    public function setOutput(OutputNodeInterface $node);
}
