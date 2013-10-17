<?php

namespace Rednose\FrameworkBundle\Entity;

use FOS\UserBundle\Entity\Group as BaseGroup;
use Doctrine\ORM\Mapping as ORM;

/**
 * A Usergroup
 *
 * @ORM\Entity
 * @ORM\Table(name="rednose_framework_group")
 */
class Group extends BaseGroup
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
