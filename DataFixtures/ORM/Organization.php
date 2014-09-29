<?php

namespace Rednose\FrameworkBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Rednose\FrameworkBundle\Model\OrganizationManagerInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Rednose\FrameworkBundle\Entity\Organization;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OrganizationFixture extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var OrganizationManagerInterface
     */
    protected $manager;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->manager = $container->get('rednose_framework.organization_manager');
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $em)
    {
        $organization = new Organization();

        $organization->setName('RedNose');
        $this->manager->updateOrganization($organization);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 5;
    }
}
