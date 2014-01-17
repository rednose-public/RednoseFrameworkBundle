<?php

namespace Rednose\FrameworkBundle\Graph\Model;

/**
 * A connection between 2 nodes within a graph, either uni- or bidirectional.
 */
class Edge
{
    /**
     * @var Node
     */
    public $input;

    /**
     * @var Node
     */
    public $output;

    /**
     * @var bool
     */
    public $bidirectional;
}
