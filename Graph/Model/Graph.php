<?php

namespace Rednose\FrameworkBundle\Graph\Model;

/**
 * A graph containing nodes and connections
 */
class Graph
{
    /**
     * @var bool
     */
    public $portrait;

    /**
     * @var Node[]
     */
    public $nodes;

    /**
     * @var Edge[]
     */
    public $edges;
}
