<?php

namespace Rednose\FrameworkBundle\Test;

use Doctrine\ORM\EntityManager;
use Rednose\FrameworkBundle\Entity\Organization;
use Rednose\FrameworkBundle\Model\OrganizationInterface;
use Rednose\FrameworkBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class WebTestCase extends BaseWebTestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var EntityManager
     */
    protected $em;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();

        // Purge DB
        $this->em = $this->container->get('doctrine.orm.entity_manager');
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);

        $purger = new ORMPurger();
        $purger->purge($this->em->getConnection());

        $this->em->clear();

        // Create required system user.
        $util = $this->container->get('fos_user.util.user_manipulator');

        /** @var UserInterface $admin */
        $admin = $util->create('admin', 'adminpasswd', 'info@rednose.nl', true, true);
        $this->em->persist($admin);

        $organization = new Organization();
        $organization->setName('Test');
        $organization->setLocale('en_GB');
        $organization->setLocalizations(['en_GB', 'nl_NL']);
        $this->em->persist($organization);

        // Create test user.
        /** @var UserInterface $admin */
        $user = $util->create('user', 'userpasswd', 'user@rednose.nl', true, true);
        $user->setOrganization($organization);
        $this->em->persist($user);

        $this->em->flush();
        $this->em->clear();
    }

    /**
     * @param string $name
     *
     * @return OrganizationInterface
     */
    protected function getOrganization($name)
    {
        return $this->em->getRepository('RednoseFrameworkBundle:Organization')->findOneBy(['name' => $name]);
    }
}
