<?php

namespace Rednose\FrameworkBundle\Tests\Security\Authorization\Voter;

use Rednose\FrameworkBundle\Entity\Group;
use Rednose\FrameworkBundle\Entity\Organization;
use Rednose\FrameworkBundle\Model\OrganizationInterface;
use Rednose\FrameworkBundle\Security\Authorization\Voter\HasOrganizationVoter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class HasOrganizationVoterTest extends AbstractVoterTest
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

        $token = $this->getToken($organization1, 3);
        $user  = $token->getUser();

        $group1 = $this->getGroup($organization1);
        $group2 = $this->getGroup($organization2);

        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $this->voter->vote($token, $user, ['VIEW']));
        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $this->voter->vote($token, $group1, ['EDIT']));

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $this->voter->vote($token, $group1, ['VIEW']));
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $this->voter->vote($token, $group2, ['VIEW']));
    }


    protected function getGroup(OrganizationInterface $organization)
    {
        $group = new Group();
        $group->setOrganization($organization);

        return $group;
    }
}
