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

use FOS\UserBundle\Entity\Group as BaseGroup;
use Doctrine\ORM\Mapping as ORM;
use Rednose\FrameworkBundle\Model\GroupInterface;

/**
 * A Usergroup
 *
 * @ORM\Entity
 * @ORM\Table(name="rednose_framework_group")
 */
class Group extends BaseGroup implements GroupInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

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

    public function setUsers($users)
    {
        $this->users = $users;
    }

    public function getUsers()
    {
        return $this->users;
    }

    public function __toString()
    {
        return (string) $this->name;
    }
}
