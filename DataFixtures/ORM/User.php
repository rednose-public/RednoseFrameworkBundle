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
    
    public function load(ObjectManager $em)
    {
        $this->userUtil->create('admin', 'libbitadmin', 'info@libbit.org', true, true);
        $this->userUtil->create('user', 'libbituser', 'user@libbit.org', true, false);
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->userUtil = $this->container->get('fos_user.util.user_manipulator');
    }
    
    public function getOrder()
    {
        return 0;
    }
}
