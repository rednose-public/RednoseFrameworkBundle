<?php

namespace Rednose\FrameworkBundle\EventListener;

use Rednose\FrameworkBundle\Assigner\AssignerInterface;
use Rednose\FrameworkBundle\Assigner\OrganizationAssigner;
use Rednose\FrameworkBundle\Entity\Organization;
use Rednose\FrameworkBundle\Entity\User;
use Rednose\FrameworkBundle\Event\UserEvent;
use Rednose\FrameworkBundle\Events;

class OrganizartionAssignerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AssignerInterface
     */
    var $assigner;

    public function setUp()
    {
        //$this->assigner = new OrganizationAssigner();
    }

}
