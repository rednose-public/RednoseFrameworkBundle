<?php

namespace Rednose\FrameworkBundle\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Rednose\FrameworkBundle\Entity\Organization;
use Rednose\FrameworkBundle\Model\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class HookContext implements Context, KernelAwareContext
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * {@inheritdoc}
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @BeforeScenario
     */
    public function purgeDatabase(BeforeScenarioScope $scope)
    {
        $entityManager = $this->getService('doctrine.orm.entity_manager');
        $entityManager->getConnection()->getConfiguration()->setSQLLogger(null);

        $entityManager->getConnection()->executeUpdate("SET foreign_key_checks = 0;");

        $purger = new ORMPurger($entityManager);
        $purger->purge();

        $entityManager->getConnection()->executeUpdate("SET foreign_key_checks = 1;");
        $entityManager->clear();

        // Create required system user.
        $util = $this->getContainer()->get('fos_user.util.user_manipulator');

        /** @var UserInterface $admin */
        $admin = $util->create('admin', 'adminpasswd', 'info@rednose.nl', true, true);

        $organization = new Organization();
        $organization->setName('Test');
        $entityManager->persist($organization);

        $admin->setOrganization($organization);
        $entityManager->persist($admin);

        $entityManager->flush();
    }

    /**
     * Get service by id.
     *
     * @param string $id
     *
     * @return object
     */
    protected function getService($id)
    {
        return $this->getContainer()->get($id);
    }

    /**
     * Returns Container instance.
     *
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->kernel->getContainer();
    }
} 