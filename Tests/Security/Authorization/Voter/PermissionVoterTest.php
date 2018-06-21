<?php

namespace Rednose\FrameworkBundle\Tests\Security\Authorization\Voter;

use Rednose\FrameworkBundle\Model\Organization;
use Rednose\FrameworkBundle\Security\Authorization\Voter\PermissionVoter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class PermissionVoterTest extends AbstractVoterTest
{
    /**
     * @var PermissionVoter
     */
    protected $voter;

    public function setUp()
    {
        $this->voter = new PermissionVoter();
    }

    public function testSupportsAttribute()
    {
        $this->assertTrue($this->voter->supportsAttribute('PERMISSION_BLA'));
        $this->assertFalse($this->voter->supportsAttribute('ROLE_BLA'));
    }

    public function testSupportsClass()
    {
        $this->assertFalse($this->voter->supportsClass('Rednose\FrameworkBundle\Entity\User'));
    }

    public function testVote()
    {
        $organization = new Organization();

        $token = $this->getToken($organization, 1);

        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $this->voter->vote($token, null, ['ROLE_BLA']));
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $this->voter->vote($token, null, ['PERMISSION_APP_BANANA']));
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $this->voter->vote($token, null, ['PERMISSION_APP_APPLE']));
    }
}
