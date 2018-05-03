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

use Rednose\FrameworkBundle\Entity\HasConditionsTrait;
use Rednose\FrameworkBundle\Entity\Theme;

class Organization implements OrganizationInterface
{
    use HasConditionsTrait;

    protected $id;
    protected $name;
    protected $dictionary;
    protected $locale;
    protected $localizations;
    protected $theme;
    protected $roleCollections;

    /**
     * Set the id
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get the id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set organization name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get organization
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set organization default locale
     *
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Get organization default locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return Theme
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @param Theme $theme
     */
    public function setTheme(Theme $theme = null)
    {
        $this->theme = $theme;
    }

    /**
     * Set available localizations
     *
     * @param array $localizations
     */
    public function setLocalizations($localizations)
    {
        $this->localizations = $localizations;
    }

    /**
     * Get available localizations
     *
     * @return $localizations
     */
    public function getLocalizations()
    {
        return $this->localizations;
    }

    /**
     * {@inheritdoc}
     */
    public function addRoleCollection(RoleCollectionInterface $roleCollection)
    {
        $this->roleCollections->add($roleCollection);
    }

    /**
     * {@inheritdoc}
     */
    public function getRoleCollections()
    {
        return $this->roleCollections;
    }
}
