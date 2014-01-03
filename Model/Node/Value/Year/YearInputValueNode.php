<?php

namespace Rednose\FrameworkBundle\Model\Node\Value\Year;

use Rednose\FrameworkBundle\Model\Node\Value\InputValueNodeInterface;

abstract class YearInputValueNode implements InputValueNodeInterface
{
    protected $offset;

    /**
     * Sets the start of year offset in months.
     *
     * @param integer $offset
     */
    public function setMonthOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * Gets the start of year offset in months.
     *
     * @return integer
     */
    public function getMonthOffset()
    {
        return $this->offset;
    }

    /**
     * @return string
     */
    public function getOutputValue()
    {
        $now = new \DateTime();

        date_add($now, date_interval_create_from_date_string($this->offset.' months'));

        return $now->format('Y');
    }
}
