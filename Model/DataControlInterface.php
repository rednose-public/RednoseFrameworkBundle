<?php

namespace Rednose\FrameworkBundle\Model;

use Rednose\FrameworkBundle\Model\DataDictionaryInterface;

interface DataControlInterface
{
    const TYPE_COMPOSITE  = 'composite';
    const TYPE_BOOLEAN    = 'boolean';
    const TYPE_DATE       = 'date';
    const TYPE_NUMBER     = 'number';
    const TYPE_COLLECTION = 'collection';
    const TYPE_STRING     = 'string';
    const TYPE_HTML       = 'html';

    /**
     * @return integer
     */
    public function getId();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $type
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param mixed $parent
     */
    public function setParent($parent);

    /**
     * @return mixed
     */
    public function getParent();

    /**
     * @return DataControlInterface[]
     */
    public function getChildren();

    /**
     * @param DataDictionaryInterface $dictionary
     */
    public function setDictionary(DataDictionaryInterface $dictionary);

    /**
     * @return DataDictionaryInterface
     */
    public function getDictionary();

    /**
     * @return string
     */
    public function getPath();

   /**
     * @param string $name
     *
     * @return bool
     */
    public function hasChild($name);
}
