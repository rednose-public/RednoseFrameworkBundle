<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\User as BaseUser;
use Rednose\FrameworkBundle\Model\OrganizationInterface;
use Rednose\FrameworkBundle\Model\RoleCollectionInterface;
use Rednose\FrameworkBundle\Model\UserInterface;
use Symfony\Component\Security\Core\User\UserInterface as CoreUserInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * A RedNose framework user
 *
 * @ORM\Entity
 * @ORM\Table(name="rednose_framework_user")
 */
class User extends BaseUser implements UserInterface
{
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_ADMIN_CHECK_PREFIX = 'PERMISSION_ADMIN_';

    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @Serializer\Groups({"list", "details"})
     */
    protected $id;

    /**
     * @Serializer\Groups({"list", "details"})
     */
    protected $username;

    /**
     * @Serializer\Groups({"list", "details"})
     *
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    protected $realname;

    /**
     * @ORM\ManyToMany(targetEntity="Rednose\FrameworkBundle\Entity\Group", inversedBy="users")
     *
     * @ORM\JoinTable(name="rednose_framework_user_group",
     *   joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;

    /**
     * @Serializer\Groups({"list", "details"})
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $locale;

    /**
     * @ORM\ManyToOne(targetEntity="Rednose\FrameworkBundle\Entity\Organization")
     *
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     **/
    protected $organization;

    /**
     * @ORM\ManyToMany(targetEntity="RoleCollection")
     *
     * @ORM\JoinTable(
     *      name="rednose_framework_user_role_collection",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="rolecollection_id", referencedColumnName="id", unique=true)}
     * )
     *
     * @var RoleCollection[]
     */
    protected $roleCollections;

    /**
     * @Serializer\Groups({"list", "details"})
     * @Serializer\Accessor("getOrganizationName")
     */
    protected $organizationName;

    /**
     * Static users will never be automatically assigned
     *
     * @Serializer\Groups({"list", "details"})
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $static = true;

    /**
     * @Serializer\Groups({"list", "details"})
     */
    protected $enabled;

    /**
     * @Serializer\Groups({"list", "details"})
     */
    protected $locked;

    /**
     * @Serializer\Groups({"list", "details"})
     */
    protected $expired;

    /**
     * @Serializer\Groups({"list", "details"})
     * @Serializer\Accessor("isAdmin")
     */
    protected $admin;

    /**
     * @Serializer\Groups({"list", "details"})
     * @Serializer\Accessor("isSuperAdmin")
     */
    protected $superAdmin;

    /**
     * @Serializer\Groups({"list", "details"})
     */
    protected $email;

    /**
     * @Serializer\Type("DateTime")
     * @Serializer\Groups({"list", "details"})
     */
    protected $lastLogin;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->roleCollections = new ArrayCollection();

        parent::__construct();
    }

    /**
     * Gets the username
     *
     * Will automatically return the username in lowercase for
     * framework compatibility.
     *
     * if forceLowercase is set to false it will return the
     * username as it has been set by setUsername().
     *
     * @param $forceLowercase
     *
     * @return string
     */
    public function getUsername($forceLowercase = true)
    {
        if ($forceLowercase) {
            return strtolower($this->username);
        }

        return $this->username;
    }

    /**
     * Get the realname (full name)
     *
     * @param string $realName
     */
    public function setRealname($realName)
    {
        $this->realname = $realName;
    }

    /**
     * Gets the realname (full name)
     *
     * @return string
     */
    public function getRealname()
    {
        return $this->realname;
    }

    /**
     * Returns the realname if set, otherwise uses
     * the username
     *
     * @return string
     */
    public function getBestname()
    {
        if ($this->getRealname()) {
            return $this->getRealname();
        }

        return $this->getUsername();
    }

    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    public function getCredentialsExpireAt()
    {
        return $this->credentialsExpireAt;
    }

    /**
     * Tells if the the given user has super admin role.
     *
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->hasRole(static::ROLE_ADMIN);
    }

    /**
     * Sets the admin status
     *
     * @param boolean $boolean
     */
    public function setAdmin($boolean)
    {
        if (true === $boolean) {
            $this->addRole(static::ROLE_ADMIN);
        } else {
            $this->removeRole(static::ROLE_ADMIN);
        }
    }

    /**
     * Set the static status
     *
     * @param boolean $static
     */
    public function setStatic($static = false)
    {
        $this->static = $static;
    }

    /**
     * Tells if this is a static user
     */
    public function isStatic()
    {
        return $this->static;
    }

    /**
     * Gets the preferred locale for this user.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Sets the preferred locale for this user.
     *
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Get the roles for this user.
     *
     * ROLE_ADMIN will automatically be added or removed depending on the PERMISSIONS this user has.
     *
     * If the user has at least one PERMISSION in any organization it will be considered an ROLE_ADMIN.
     *
     * @return array
     */
    public function getRoles()
    {
        $roles   = parent::getRoles();
        $isAdmin = false;

        foreach ($this->getRoleCollections() as $roleCollection) {
            $collectionRoles = $roleCollection->getRoles();

            foreach ($collectionRoles as $role) {
                if (substr(strtoupper($role), 0, strlen(self::ROLE_ADMIN_CHECK_PREFIX)) === self::ROLE_ADMIN_CHECK_PREFIX) {
                    $isAdmin = true;

                    break;
                }
            }
        }

        if ($isAdmin === true) {
            $roles[] = self::ROLE_ADMIN;
        } else {
            if (($adminRole = array_search(self::ROLE_ADMIN, $roles)) !== false) {
                unset($roles[$adminRole]);
            }
        }

        return array_values(array_filter(array_unique($roles)));
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissions()
    {
        $permissions = [];

        foreach ($this->getRoleCollections() as $roleCollection) {
            $collectionRoles = $roleCollection->getRoles();

            if ($roleCollection->getOrganization() === null || $this->getOrganization() === null) {
                continue;
            }

            if ($roleCollection->getOrganization()->getId() === $this->getOrganization()->getId()) {
                $permissions = array_merge($permissions, $collectionRoles);
            }
        }

        return array_values(array_filter(array_unique($permissions)));
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
    public function setRoleCollections($roleCollections)
    {
        $this->roleCollections = $roleCollections;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoleCollections()
    {
        return $this->roleCollections;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableOrganizations()
    {
        $organizations = [];

        foreach ($this->getRoleCollections() as $roleCollection) {
            $organizations[] = $roleCollection->getOrganization();
        }

        return $organizations;
    }

    /**
     * Gets the name of the preferred organization for this user.
     *
     * @return string
     */
    public function getOrganizationName()
    {
        if (!$this->organization) {
            return '';

        }

        return $this->organization->getName();
    }

    /**
     * Sets the preferred organization for this user.
     *
     * @param OrganizationInterface $organization
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }

    /**
     * Symfony\Component\Security\Core\User\EquatableInterface::isEqualTo()
     */
    public function isEqualTo(CoreUserInterface $user)
    {
        return $this->getUsername() === $user->getUsername();
    }
}
