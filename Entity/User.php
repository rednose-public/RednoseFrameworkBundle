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

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
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

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Serializer\Groups({"list", "details"})
     */
    protected $id;

    /**
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
     * @ORM\Column(type="string", nullable=true)
     */
    protected $locale;

    /**
     * Transient API property.
     *
     * @Serializer\SerializedName("username")
     * @Serializer\Accessor(getter="getBestName")
     * @Serializer\Groups({"list", "details"})
     */
    protected $bestName;

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
     * Symfony\Component\Security\Core\User\EquatableInterface::isEqualTo()
     */
    public function isEqualTo(CoreUserInterface $user)
    {
        return $this->getUsername() === $user->getUsername();
    }
}
