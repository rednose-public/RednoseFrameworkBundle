<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Twig\Extension;

use \HTMLPurifier;

class PurifyExtension extends \Twig_Extension
{
    /**
     * @var HTMLPurifier
     */
    protected $purifier;

    /**
     * Constructor.
     *
     * @param HTMLPurifier $purifier
     */
    public function __construct(HTMLPurifier $purifier)
    {
        $this->purifier = $purifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            'rednose_framework_purify' => new \Twig_Filter_Method($this, 'purify', array('is_safe' => array('html'))),
        );
    }

    /**
     * @param string $html
     *
     * @return string
     */
    public function purify($html)
    {
        return $this->purifier->purify($html);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'rednose_framework_purify';
    }
}
