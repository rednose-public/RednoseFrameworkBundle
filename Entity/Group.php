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

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\Group as BaseGroup;
use Rednose\FrameworkBundle\Model\GroupInterface;
use Rednose\FrameworkBundle\Model\OrganizationInterface;
use Rednose\FrameworkBundle\Model\UserInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * A User-group
 *
 * @ORM\Entity
 * @ORM\Table(name="rednose_framework_group")
 * @ORM\HasLifecycleCallbacks()
 */
class Group extends BaseGroup implements GroupInterface
{
    use HasConditionsTrait;
    use HasOrganizationTrait;

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
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="Rednose\FrameworkBundle\Entity\User", mappedBy="groups")
     */
    protected $users;

    /**
     * Constructor.
     *
     * Override of the superclass so we can construct this object without specifying a name.
     *
     * @param string $name
     * @param array  $roles
     */
    public function __construct($name = null, $roles = array())
    {
        parent::__construct($name, $roles);
    }

    /**
     * @param UserInterface[] $users
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }

    /**
     * @return UserInterface[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @return OrganizationInterface
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param OrganizationInterface $organization
     */
    public function setOrganization(OrganizationInterface $organization)
    {
        $this->organization = $organization;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->name;
    }
}
