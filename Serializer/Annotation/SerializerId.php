<?php

namespace Rednose\FrameworkBundle\Serializer\Annotation;

/**
 * @Annotation
 */
final class SerializerId
{
    /**
     * @var string
     */
    public $type = null;

    /**
     * @var string
     */
    public $property = null;
}
