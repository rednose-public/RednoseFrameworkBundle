<?php

namespace Rednose\FrameworkBundle\Model;

interface DataControlInterface
{
    const TYPE_COMPOSITE  = 'composite';
    const TYPE_COLLECTION = 'collection';
    const TYPE_DATE       = 'date';
    const TYPE_BOOLEAN    = 'boolean';
    const TYPE_NUMBER     = 'number';
    const TYPE_STRING     = 'string';
    const TYPE_TEXT       = 'text';
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
     * @param integer $sortOrder
     */
    public function setSortOrder($sortOrder);

    /**
     * @return integer
     */
    public function getSortOrder();

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

    /**
     * @param string $name
     *
     * @return bool
     */
    public function getChild($name);

    /**
     * @return string
     */
    public function getIcon();

    /**
     * @return bool
     */
    public function hasChildren();

    /**
     * A control is relative when it has at least one ancestor of the type `collection`
     *
     * @return bool
     */
    public function isRelative();

    /**
     * @param string $context XPath location
     *
     * @return bool
     */
    public function isInContext($context);

    /**
     * @param DataControlInterface $child
     */
    public function addChild(DataControlInterface $child);
}
