<?php

namespace Libbit\FrameworkBundle\DataFixtures\ORM;
 
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserFixture extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;
    private $userUtil;
    private $groupManager;
    
    public function load(ObjectManager $em)
    {
        $admin = $this->userUtil->create('admin', 'libbitadmin', 'info@libbit.org', true, true);
        $user = $this->userUtil->create('user', 'libbituser', 'user@libbit.org', true, false);
        
        foreach ($this->groupManager->findGroups() as $group) {
            $admin->addGroup($group);
            
            if ($group->getName() != 'Administrator') {
                $user->addGroup($group);
            }
        }
        
        $em->persist($admin);
        $em->persist($user);
        $em->flush();
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->userUtil = $this->container->get('fos_user.util.user_manipulator');
        $this->groupManager = $this->container->get('fos_user.group_manager');
    }
    
    public function getOrder()
    {
        return 1;
    }
}
