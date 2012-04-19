<?php

namespace Libbit\FrameworkBundle\DataFixtures\ORM;

use Libbit\FrameworkBundle\Entity\Group;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class GroupFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $em)
    {
        $groupIndifidual = new Group('Individual', array('E_USER_HIMSELF'));
        $groupUser = new Group('User', array('E_USER'));
        $groupAdministrator = new Group('Administrator', array('E_USER', 'E_ADMIN'));
        
        $em->persist($groupIndifidual);
        $em->persist($groupUser);
        $em->persist($groupAdministrator);
        
        $em->flush();
    }

    public function getOrder()
    {
        return 0;
    }
}
