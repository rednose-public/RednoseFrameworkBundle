<?php

namespace Rednose\FrameworkBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Rednose\FrameworkBundle\Entity\RoleCollection;
use Rednose\FrameworkBundle\Entity\User;
use Rednose\FrameworkBundle\Model\Organization;
use Rednose\FrameworkBundle\Model\UserInterface;

class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @var RoleCollection
     */
    private $roleCollection;

    public function setUp()
    {
        $this->user = new User();
        $this->user->setUsername('Oscar');

        $this->roleCollection = new RoleCollection();
        $this->roleCollection->setRoles(['ROLE_ADMIN_APP_SOMETHING', 'ROLE_ADMIN_APP_SOMETHING', 'ROLE_ADMIN_APP_SOMETHING_ELSE']);
    }

    public function testUsername()
    {
        $this->assertSame('oscar', $this->user->getUsername());
        $this->assertSame('Oscar', $this->user->getUsername(false));
    }

    public function testGetBestname()
    {
        $this->assertSame('oscar', $this->user->getBestname());

        $this->user->setRealname('Oscar the Grouch');

        $this->assertSame('Oscar the Grouch', $this->user->getBestname());
    }

    public function testRolesNoRoleCollection()
    {
        $this->user->addRole('ROLE_TEST');
        $this->user->addRole('ROLE_TEST');
        $this->user->addRole('ROLE_TEST');

        $this->assertSame(['ROLE_TEST', 'ROLE_USER'], $this->user->getRoles());
    }

    public function testRolesWithRoleCollectionMissingOrganization()
    {
        $this->user->addRole('ROLE_TEST');
        $this->user->addRole('ROLE_TEST');

        $this->user->addRoleCollection($this->roleCollection);

        $this->assertSame(['ROLE_TEST', 'ROLE_USER', 'ROLE_ADMIN'], $this->user->getRoles());

        $this->user->setRoleCollections(new ArrayCollection());

        $this->assertSame(['ROLE_TEST', 'ROLE_USER'], $this->user->getRoles());
    }

    public function testRolesWithRoleCollectionWithMatchingOrganization()
    {
        $organization = new Organization();
        $organization->setId('123');
        $organization->setName('Test');

        $this->user->setOrganization($organization);
        $this->roleCollection->setOrganization($organization);

        $this->user->addRole('ROLE_TEST');
        $this->user->addRole('ROLE_TEST');

        $this->user->addRoleCollection($this->roleCollection);

        $this->assertSame(['ROLE_TEST', 'ROLE_USER', 'ROLE_ADMIN_APP_SOMETHING', 'ROLE_ADMIN_APP_SOMETHING_ELSE', 'ROLE_ADMIN'], $this->user->getRoles());

        $this->user->setRoleCollections(new ArrayCollection());

        $this->assertSame(['ROLE_TEST', 'ROLE_USER'], $this->user->getRoles());
    }

    public function testRolesWithRoleCollectionWithNonMatchingOrganization()
    {
        $organization = new Organization();
        $organization->setId('123');
        $organization->setName('Test');

        $organization2 = new Organization();
        $organization->setId('456');
        $organization2->setName('Test2');

        $this->user->setOrganization($organization);
        $this->roleCollection->setOrganization($organization2);

        $this->user->addRole('ROLE_TEST');
        $this->user->addRole('ROLE_TEST');

        $this->user->addRoleCollection($this->roleCollection);

        $this->assertSame(['ROLE_TEST', 'ROLE_USER', 'ROLE_ADMIN'], $this->user->getRoles());

        $this->user->setRoleCollections(new ArrayCollection());

        $this->assertSame(['ROLE_TEST', 'ROLE_USER'], $this->user->getRoles());
    }

    public function testIsEqualTo()
    {
        $organization = new Organization();
        $organization->setName('RobCo electronics');

        $organization2 = new Organization();
        $organization2->setName('Red Rocket Gas');

        $this->user->setUsername('JohnDoe');
        $this->user->setOrganization($organization);

        $user = clone $this->user;
        $user->setUsername('JaneDoe');
        $this->assertFalse($this->user->isEqualTo($user));

        $user = clone $this->user;
        $user->setOrganization($organization2);
        $this->assertFalse($this->user->isEqualTo($user));

        $user = clone $this->user;
        $this->assertTrue($this->user->isEqualTo($user));
    }
}