<?php

namespace Rednose\FrameworkBundle\Model;

use JMS\Serializer\Annotation as Serializer;

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
     * @param string $separator
     *
     * @return bool
     */
    public function hasControl($path, $separator = '/');

    /**
     * @param string $path
     * @param string $separator
     *
     * @return DataControlInterface
     */
    public function getControl($path, $separator = '/');

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasChild($name);

    /**
     * @param string $name
     *
     * @return DataControlInterface
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
     * @param array  $types   Control types to include. If left empty, all types are returned.
     * @param string $context XPath location for the context node.
     *                          Can be used to include relative nodes. If no context location is specified,
     *                          only controls with absolute paths are returned.
     *
     * @return array
     */
    public function toList(array $types = array(), $context = null);

    /**
     * @return \DOMDocument|null
     */
    public function getTestData();

    /**
     * @param \DOMDocument|null $data
     */
    public function setTestData($data);

    /**
     * Return the dictionary as an object
     *
     * @return Object
     */
    public function toObject();
}
