<?php

namespace Rednose\FrameworkBundle\Test;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Rednose\FrameworkBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class WebTestCase extends BaseWebTestCase
{
    public function setUp()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        // Purge DB
        $em = $container->get('doctrine.orm.entity_manager');
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        $em->getConnection()->executeUpdate("SET foreign_key_checks = 0;");

        $purger = new ORMPurger($em);
        $purger->purge();

        $em->getConnection()->executeUpdate("SET foreign_key_checks = 1;");
        $em->clear();
    }
}
