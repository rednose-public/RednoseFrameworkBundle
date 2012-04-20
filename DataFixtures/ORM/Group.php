<?php

namespace Libbit\FrameworkBundle\DataFixtures\ORM;

use Libbit\FrameworkBundle\Entity\Group;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GroupFixture extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;
    private $groupManager;
    
    public function load(ObjectManager $em)
    {
        $groupUser = new Group('User', array('ROLE_USER'));
        $groupAdministrator = new Group('Administrator', array('ROLE_USER', 'ROLE_ADMIN'));
        
        $this->groupManager->updateGroup($groupUser, false);
        $this->groupManager->updateGroup($groupAdministrator, false);
        
        $em->flush();
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->groupManager = $this->container->get('fos_user.group_manager');
    }

    public function getOrder()
    {
        return 0;
    }
}
