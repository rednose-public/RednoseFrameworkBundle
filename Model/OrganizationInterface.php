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

use Rednose\FrameworkBundle\Entity\HasConditionsInterface;

interface OrganizationInterface extends HasConditionsInterface
{
    /**
     * Set the id
     *
     * @param string $id
     */
    public function setId($id);

    /**
     * Get the id
     *
     * @return string
     */
    public function getId();

    /**
     * Set organization name
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Get organization
     *
     * @return string
     */
    public function getName();

    /**
     * Set organization default locale
     *
     * @param string $locale
     */
    public function setLocale($locale);

    /**
     * Get organization default locale
     *
     * @return string
     */
    public function getLocale();

    /**
     * Set available localizations
     *
     * @param array $localizations
     */
    public function setLocalizations($localizations);

    /**
     * Get available localizations
     *
     * @return $localizations
     */
    public function getLocalizations();

    /**
     * @param RoleCollectionInterface $roleCollection
     */
    public function addRoleCollection(RoleCollectionInterface $roleCollection);

    /**
     * @return RoleCollectionInterface[]
     */
    public function getRoleCollections();
}
