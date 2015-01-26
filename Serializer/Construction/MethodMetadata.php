<?php

namespace Rednose\FrameworkBundle\Serializer\Construction;

use Metadata\MethodMetadata as BaseMetaData;

class MethodMetadata extends BaseMetaData
{
    private $listener;

    /**
     * {@inheritdoc}
     */
    public function invoke($obj, array $args = array())
    {
        $this->listener->{$this->name}();
    }

    public function setListener($listener)
    {
        $this->listener = $listener;
    }
}
