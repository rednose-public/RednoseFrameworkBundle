<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
