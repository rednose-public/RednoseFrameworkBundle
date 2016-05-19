<?php

namespace Doctanium\Bundle\DataProviderBundle\EventListener;

use Rednose\FrameworkBundle\Entity\User;
use Rednose\FrameworkBundle\Event\UserEvent;
use Rednose\FrameworkBundle\EventListener\AssignerListener;
use Rednose\FrameworkBundle\Events;

class AssignerListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AssignerListener
     */
    protected $listener;

    protected $organizationAssigner;
    protected $groupAssigner;

    public function setUp()
    {
        $this->organizationAssigner = $this->getMockBuilder('Rednose\FrameworkBundle\Assigner\OrganizationAssigner')
            ->disableOriginalConstructor()
            ->getMock();

        $this->groupAssigner = $this->getMockBuilder('Rednose\FrameworkBundle\Assigner\GroupAssigner')
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->listener = new AssignerListener($this->organizationAssigner, $this->groupAssigner);
    }

    public function testGetSubscriptions()
    {
        $expected = [Events::USER_LOGIN => [
            ['handleOrganizationAssign', 128],
            ['handleGroupAssign', 0]
        ]];

        $events = $this->listener->getSubscribedEvents();

        $this->assertSame($expected, $events);
    }

    public function testHandleOrganizationAssign()
    {
        $user = new User();
        $event = new UserEvent($user);

        $this->organizationAssigner->expects($this->once())->method('assign');
        $this->listener->handleOrganizationAssign($event);
    }

    public function testHandleOrganizationAssignStatic()
    {
        $user = new User();
        $user->setStatic(true);

        $event = new UserEvent($user);

        $this->organizationAssigner->expects($this->never())->method('assign');
        $this->listener->handleOrganizationAssign($event);
    }

    public function testHandleGroupAssign()
    {
        $user = new User();
        $event = new UserEvent($user);

        $this->groupAssigner->expects($this->once())->method('assign');
        $this->listener->handleGroupAssign($event);
    }

    public function testHandleGroupAssignStatic()
    {
        $user = new User();
        $user->setStatic(true);

        $event = new UserEvent($user);

        $this->groupAssigner->expects($this->never())->method('assign');
        $this->listener->handleGroupAssign($event);
    }
}