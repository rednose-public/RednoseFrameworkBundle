<?php

namespace Rednose\FrameworkBundle\Model\Node;

/**
 * A node within a graph. Currently limited to single input and output.
 */
interface OutputNodeInterface
{
    /**
     * @return InputNodeInterface
     */
    public function getInput();

    /**
     * @param InputNodeInterface $node
     */
    public function setInput(InputNodeInterface $node);
}
