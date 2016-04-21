<?php

namespace Rednose\FrameworkBundle\Tests\Security\Authorization\Voter;

use Rednose\FrameworkBundle\Entity\Group;
use Rednose\FrameworkBundle\Entity\Organization;
use Rednose\FrameworkBundle\Entity\User;
use Rednose\FrameworkBundle\Model\OrganizationInterface;
use Rednose\FrameworkBundle\Security\Authorization\Voter\HasOrganizationVoter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class HasOrganizationVoterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HasOrganizationVoter
     */
    protected $voter;

    public function setUp()
    {
        $this->voter = new HasOrganizationVoter();
    }

    public function testSupportsAttribute()
    {
        $this->assertTrue($this->voter->supportsAttribute('VIEW'));
        $this->assertFalse($this->voter->supportsAttribute('EDIT'));
    }

    public function testSupportsClass()
    {
        $this->assertTrue($this->voter->supportsClass('Rednose\FrameworkBundle\Entity\Group'));
        $this->assertFalse($this->voter->supportsClass('Rednose\FrameworkBundle\Entity\User'));
    }

    public function testVote()
    {
        $organization1 = new Organization('Organization 1');
        $organization2 = new Organization('Organization 2');

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken')
            ->disableOriginalConstructor()
            ->getMock();

        $user = $this->getUser($organization1);

        $token->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($user));

        $group1 = $this->getGroup($organization1);
        $group2 = $this->getGroup($organization2);

        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $this->voter->vote($token, $user, ['VIEW']));
        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $this->voter->vote($token, $group1, ['EDIT']));

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $this->voter->vote($token, $group1, ['VIEW']));
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $this->voter->vote($token, $group2, ['VIEW']));
    }

    protected function getUser(OrganizationInterface $organization)
    {
        $user = new User();
        $user->setOrganization($organization);

        return $user;
    }

    protected function getGroup(OrganizationInterface $organization)
    {
        $group = new Group();
        $group->setOrganization($organization);

        return $group;
    }
}
