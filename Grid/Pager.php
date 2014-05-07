<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Grid;

/**
 * Class to render a pager for a gridview.
 */
class Pager
{
    /**
     * @var int
     */
    protected $pageLength;

    /**
     * @var int
     */
    protected $page;

    /**
     * @var int
     */
    protected $totalResults;

    /**
     * Default constructor.
     */
    public function __construct()
    {
        $this->pageLength = 10;
    }

    /**
     * Return the defined pagelength
     *
     * @return integer The length.
     */
    public function getPageLength()
    {
        return $this->pageLength;
    }

    /**
     * Set the length for the pages.
     *
     * @param integer $length The length.
     */
    public function setPageLength($length)
    {
        $this->pageLength = $length;
    }

    /**
     * Get the current offset.
     *
     * @return integer The offset.
     */
    public function getOffset()
    {
        return ($this->page * $this->pageLength) - $this->pageLength;
    }

    /**
     * The current page.
     *
     * @param integer $page The page.
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * Gets the first result.
     *
     * @return integer The result.
     */
    public function getFirstResult()
    {
        return $this->getOffset() + 1;
    }

    /**
     * Gets the last result.
     *
     * @return integer The result.
     */
    public function getLastResult()
    {
        $last = $this->getOffset() + $this->pageLength;

        return $last > $this->getTotalResults() ? $this->getTotalResults() : $last;
    }

    /**
     * Gets the current page.
     *
     * @return integer The current page.
     */
    public function getCurrentPage()
    {
        return $this->page;
    }

    /**
     * Gets the next page.
     *
     * @return integer The next page.
     */
    public function getNextPage()
    {
        return ($this->page < $this->getTotalpages()) ? $this->page + 1 : false;
    }

    /**
     * Gets the previous page.
     *
     * @return integer The previous page.
     */
    public function getPreviousPage()
    {
        return ($this->getCurrentPage() > 1) ? $this->page - 1 : false;
    }

    /**
     * Gets the first page.
     *
     * @return integer The first page.
     */
    public function getFirstPage()
    {
        return 1;
    }

    /**
     * Gets the last page.
     *
     * @return integer The last page.
     */
    public function getLastPage()
    {
        return $this->getTotalPages();
    }

    /**
     * Set the total results.
     *
     * @param integer $totalResults The total.
     */
    public function setTotalResults($totalResults)
    {
        $this->totalResults = $totalResults;
    }

    /**
     * Gets the total results.
     *
     * @return integer The total.
     */
    public function getTotalResults()
    {
        return $this->totalResults;
    }

    /**
     * Gets the total number of pages.
     *
     * @return integer The total number of pages.
     */
    public function getTotalPages()
    {
        return ceil($this->getTotalResults() / $this->pageLength);
    }
}
