<?php

namespace Rednose\FrameworkBundle\Tests\Security\Authorization\Voter;

use Rednose\FrameworkBundle\Entity\RoleCollection;
use Rednose\FrameworkBundle\Entity\User;
use Rednose\FrameworkBundle\Model\OrganizationInterface;

class AbstractVoterTest extends \PHPUnit_Framework_TestCase
{
    protected function getToken($organization, $amount = 4)
    {
        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken')
            ->disableOriginalConstructor()
            ->getMock();

        $user = $this->getUser($organization);

        $token->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($user));

        return $token;
    }

    protected function getUser(OrganizationInterface $organization)
    {
        $user = new User();
        $user->setOrganization($organization);

        $rc = new RoleCollection();
        $rc->setRoles(['PERMISSION_APP_APPLE']);
        $rc->setOrganization($organization);

        $user->addRoleCollection($rc);

        return $user;
    }
}