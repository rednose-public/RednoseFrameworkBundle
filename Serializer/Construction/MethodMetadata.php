<?php

namespace Rednose\FrameworkBundle\Serializer\Construction;

use Metadata\MethodMetadata as BaseMetaData;

class MethodMetadata extends BaseMetaData
{
    private $listener;

    public function invoke($object) {
        $this->listener->{$this->name}();
    }

    public function setListener($listener) {
        $this->listener = $listener;
    }
}
