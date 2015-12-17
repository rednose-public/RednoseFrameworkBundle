<?php

namespace Rednose\FrameworkBundle\Admin;

use Rednose\FrameworkBundle\Entity\HasOrganizationInterface;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Route\RouteCollection;

abstract class AbstractAdmin extends Admin
{
    /**
     * Enable acl by default (to hide the edit ACL button)
     */
    public function isAclEnabled()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getListModes()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getNewInstance()
    {
        $instance = parent::getNewInstance();

        if (!$instance instanceof HasOrganizationInterface) {
            return $instance;
        }

        $context = $this->getConfigurationPool()->getContainer()->get('rednose_framework.organization_context');
        $instance->setOrganization($context->getOrganization());

        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);

        $context = $this->getConfigurationPool()->getContainer()->get('rednose_framework.organization_context');

        $query->andWhere(
            $query->expr()->eq($query->getRootAliases()[0].'.organization', ':organization')
        );

        $query->setParameter('organization', $context->getOrganization());

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('export');
    }
}