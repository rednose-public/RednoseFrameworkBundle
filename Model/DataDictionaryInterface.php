<?php

namespace Rednose\FrameworkBundle\Model;

use JMS\Serializer\Annotation as Serializer;
use Rednose\FrameworkBundle\Model\DataControlInterface;

/**
 * A data control list.
 */
interface DataDictionaryInterface
{
    /**
     * @param $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @return DataControlInterface[]
     */
    public function getControls();

    /**
     * @param DataControlInterface $control
     */
    public function addControl(DataControlInterface $control);

    /**
     * @param DataControlInterface $control
     */
    public function removeControl(DataControlInterface $control);
}
