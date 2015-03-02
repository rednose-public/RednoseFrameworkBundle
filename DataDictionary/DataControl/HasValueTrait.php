<?php

namespace Rednose\FrameworkBundle\DataDictionary\DataControl;

trait HasValueTrait
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @throws \InvalidArgumentException when the argument type doesn't match the required format
     */
    public function setValue($value)
    {
        if ((
            $this->getType() === DataControlInterface::TYPE_DATE && !$value instanceof \DateTime ||
            $this->getType() === DataControlInterface::TYPE_COLLECTION && !is_array($value) ||
            $this->getType() === DataControlInterface::TYPE_NUMBER && !is_numeric($value) ||
            $this->getType() === DataControlInterface::TYPE_STRING && !is_string($value) ||
            $this->getType() === DataControlInterface::TYPE_TEXT && !is_string($value) ||
            $this->getType() === DataControlInterface::TYPE_HTML && !is_string($value)) &&
            $value !== null
        ) {
            throw new \InvalidArgumentException(sprintf('Invalid value for data control with type "%s"', $this->getType()));
        }

        $this->value = $value;
    }

    /**
     * @return string
     */
    abstract public function getType();
}