<?php

namespace Rednose\FrameworkBundle\Context;

use Rednose\FrameworkBundle\Model\OrganizationInterface;
use Rednose\FrameworkBundle\Model\UserInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class OrganizationContext
{
    /**
     * @var SecurityContextInterface
     */
    protected $context;

    /**
     * @param SecurityContextInterface $context
     */
    public function __construct(SecurityContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * @return OrganizationInterface
     */
    public function getOrganization()
    {
        if (!$this->context->getToken()) {
            return null;
        }

        $user = $this->context->getToken()->getUser();

        if (!$user instanceof UserInterface) {
            return null;
        }

       return $user->getOrganization();
    }
}