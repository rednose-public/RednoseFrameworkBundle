<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use FOS\UserBundle\Util\UserManipulator;
use Rednose\FrameworkBundle\Model\UserInterface;

class UserFixture extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var UserManipulator
     */
    private $userUtil;

    public function load(ObjectManager $em)
    {
        /** @var UserInterface $admin */
        $admin = $this->userUtil->create('admin', 'adminpasswd', 'info@rednose.nl', true, true);
        $admin->setRealname('Administrator');
        $em->persist($admin);

        /** @var UserInterface $user */
        $user = $this->userUtil->create('user', 'userpasswd', 'user@rednose.nl', true, false);
        $user->setRealname('Demo user');
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
