<?php

namespace Rednose\FrameworkBundle\Tests\Entity;

use Rednose\FrameworkBundle\Entity\RoleCollection;
use Rednose\FrameworkBundle\Entity\User;
use Rednose\FrameworkBundle\Model\UserInterface;

class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UserInterface
     */
    private $user;

    public function setUp()
    {
        $this->user = new User();
    }

    public function testUsername()
    {
        $this->user->setUsername('Test');

        $this->assertSame('test', $this->user->getUsername());
        $this->assertSame('Test', $this->user->getUsername(false));
    }

    public function testRolesNoRoleCollection()
    {
        $this->user->addRole('ROLE_TEST');
        $this->user->addRole('ROLE_TEST');
        $this->user->addRole('ROLE_TEST');

        $this->assertSame(['ROLE_TEST', 'ROLE_USER'], $this->user->getRoles());
    }

    public function testRolesWithRoleCollection()
    {
        $roleCollection = new RoleCollection();
        $roleCollection->setRoles(['ROLE_ADMIN_APP_SOMETHING', 'ROLE_ADMIN_APP_SOMETHING', 'ROLE_ADMIN_APP_SOMETHING_ELSE']);

        $this->user->addRole('ROLE_TEST');
        $this->user->addRole('ROLE_TEST');

        $this->user->addRoleCollection($roleCollection);

        $this->assertSame(['ROLE_TEST', 'ROLE_USER', 'ROLE_ADMIN_APP_SOMETHING', 'ROLE_ADMIN_APP_SOMETHING_ELSE'], $this->user->getRoles());
    }

}