<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Model;

interface OrganizationInterface
{
    /**
     * @return integer
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return DataDictionaryInterface
     */
    public function getDataDictionary();

    /**
     * @param mixed DataDictionaryInterface
     */
    public function setDataDictionary($dictionary);
}
