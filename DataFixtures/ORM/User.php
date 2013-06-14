<?php

namespace Rednose\FrameworkBundle\DataFixtures\ORM;

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
        $admin = $this->userUtil->create('admin', 'adminpasswd', 'info@libbit.org', true, true);
        $admin->addGroup($this->getReference('group-admin'));
        $em->persist($admin);

        $user = $this->userUtil->create('user', 'userpasswd', 'user@libbit.org', true, false);
        $user->addGroup($this->getReference('group-user'));
        $em->persist($user);

        $em->flush();
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->userUtil = $this->container->get('fos_user.util.user_manipulator');
    }

    public function getOrder()
    {
        return 1;
    }
}
