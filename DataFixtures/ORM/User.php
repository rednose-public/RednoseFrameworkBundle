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
        $admin->addGroup($this->getReference('group-admin'));
        $em->persist($admin);

        $user = $this->userUtil->create('user', 'libbituser', 'user@libbit.org', true, false);
        $user->addGroup($this->getReference('group-user'));
        $em->persist($user);

        $test1 = $this->userUtil->create('test1', 'test1', 'test1@libbit.org', true, false);
        $test1->addGroup($this->getReference('group-test'));
        $em->persist($test1);

        $test2 = $this->userUtil->create('test2', 'test2', 'test2@libbit.org', true, false);
        $test2->addGroup($this->getReference('group-test'));
        $em->persist($test2);
        
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
