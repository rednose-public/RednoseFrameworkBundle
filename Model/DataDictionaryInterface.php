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
     * @return string
     */
    public function getId();

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

    /**
     * Returns whether a control for a given path exists or not.
     *
     * @param string $path
     *
     * @return bool
     */
    public function hasControl($path);

    /**
     * @param string $path
     *
     * @return DataControlInterface
     */
    public function getControl($path);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasChild($name);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function getChild($name);

    /**
     * Return the dictionary as an array tree structure.
     *
     * @return array
     */
    public function toArray();

    /**
     * Return the dictionary as a list, filtered by control type.
     *
     * @param string $type
     *
     * @return array
     */
    public function toList($type);
}
